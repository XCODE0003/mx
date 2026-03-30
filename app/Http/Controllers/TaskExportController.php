<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateTaskBundle;
use App\Jobs\GenerateTaskPdf;
use App\Models\Order;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TaskExportController extends Controller
{
    /**
     * Берем по одной случайной "пачке" для каждого набора номеров заданий.
     * Для тематических наборов (задания 1-5 с общим question) выбирается один полный набор.
     * Для остальных: выбираем самую длинную пачку (например, 23-27 вместо одиночной 23).
     * При равной длине берется случайная.
     */
    private function pickGroupedPacks($groupsWithQuestion)
    {
        if ($groupsWithQuestion->isEmpty()) {
            return collect();
        }

        $packsByQuestion = $groupsWithQuestion->groupBy('question');
        
        // Проверяем, является ли это тематическим набором (задания 1-5 с одинаковым question)
        $isThematicSet = $packsByQuestion->first()->filter(function ($group) {
            $title = (string) $group->formatted_title;
            return in_array($title, ['1', '2', '3', '4', '5']);
        })->count() >= 5;
        
        // Если это тематический набор (например, ОГЭ Математика задания 1-5),
        // выбираем случайно один полный набор
        if ($isThematicSet) {
            // Выбираем случайный набор (один question с заданиями 1-5)
            $randomSet = $packsByQuestion->random();
            return $randomSet;
        }

        // Для обычных пачек (не тематические наборы) - старая логика
        $packsByRangeSignature = $packsByQuestion->groupBy(function ($pack) {
            $titles = $pack
                ->map(function ($group) {
                    return (string) $group->formatted_title;
                })
                ->filter()
                ->unique()
                ->sort(function ($a, $b) {
                    return (int) $a <=> (int) $b;
                })
                ->values()
                ->all();

            return $titles[0] ?? 'unknown';
        });

        return $packsByRangeSignature->map(function ($sameRangePacks) {
            $packsWithMetrics = $sameRangePacks->map(function ($pack) {
                $uniqueTitlesCount = $pack
                    ->map(function ($group) {
                        return (string) $group->formatted_title;
                    })
                    ->filter()
                    ->unique()
                    ->count();
                
                // Считаем минимальное количество заданий среди всех групп в пакете
                $minTasksInGroup = $pack->map(function ($group) {
                    return $group->tasks()->count();
                })->min() ?? 0;

                return [
                    'pack' => $pack,
                    'size' => $uniqueTitlesCount,
                    'min_tasks' => $minTasksInGroup,
                ];
            });

            // Приоритет: сначала по размеру пакета (больше заданий), затем по минимальному количеству заданий
            $maxSize = $packsWithMetrics->max('size');
            $candidatesWithMaxSize = $packsWithMetrics->filter(function ($item) use ($maxSize) {
                return $item['size'] === $maxSize;
            });
            
            // Среди пакетов с максимальным размером выбираем те, у которых больше заданий
            $maxTasks = $candidatesWithMaxSize->max('min_tasks');
            $bestCandidates = $candidatesWithMaxSize->filter(function ($item) use ($maxTasks) {
                return $item['min_tasks'] >= max(3, $maxTasks * 0.7); // Берём пакеты с количеством заданий >= 70% от максимума, но не менее 3
            })->pluck('pack');

            if ($bestCandidates->isEmpty()) {
                $bestCandidates = $candidatesWithMaxSize->pluck('pack');
            }

            return $bestCandidates->random();
        })->flatten(1)->values();
    }

    private function formingGroupsCollection(Subject $subject): \Illuminate\Support\Collection
    {
        if (! $subject->relationLoaded('groups')) {
            $subject->load('groups');
        }

        $baseGroups = $subject->groups->where('is_forming', true)->where('question', '=', null);
        $groupedPacks = $this->pickGroupedPacks(
            $subject->groups->where('is_forming', true)->where('question', '!=', null)
        );
        $packTitles = $groupedPacks->map(function ($group) {
            return (string) $group->formatted_title;
        })->filter()->unique();

        return $baseGroups->reject(function ($group) use ($packTitles) {
            return $packTitles->contains((string) $group->formatted_title);
        })->concat($groupedPacks);
    }

    /**
     * @return array<int>
     */
    private function sharedMarkIdsForSubject(Subject $subject): array
    {
        $map = config('task_variants.sync_mark_ids', []);
        $exam = (string) $subject->exam_type;
        $class = (string) ($subject->class_name ?? '');
        if ($class === '' || ! isset($map[$exam][$class])) {
            return [];
        }

        return array_values(array_unique(array_map('intval', (array) $map[$exam][$class])));
    }

    /**
     * @return array{variants: array<int, array<int>>, firstBaseTask: ?Task}
     */
    private function buildAutoVariants(Subject $subject, int $variantCount): array
    {
        $groups = $this->formingGroupsCollection($subject);
        if ($groups->isEmpty()) {
            return ['variants' => [], 'firstBaseTask' => null];
        }

        $subjectKey = $subject->subject_id;
        $shared = $this->sharedMarkIdsForSubject($subject);
        $pinned = [];
        foreach ($shared as $markId) {
            $g = $groups->firstWhere('id', $markId);
            if ($g) {
                $t = $g->tasks()->where('subject_id', $subjectKey)->with('group')->inRandomOrder()->first();
                if ($t) {
                    $pinned[$markId] = $t->id;
                }
            }
        }

        $variants = [];
        $usedTaskIds = [];
        
        for ($v = 0; $v < $variantCount; $v++) {
            $tasks = $groups->map(function ($group) use ($subjectKey, $pinned, &$usedTaskIds, $v) {
                if (isset($pinned[$group->id])) {
                    return Task::with('group')->find($pinned[$group->id]);
                }

                // Избегаем повторов: выбираем задания, которых ещё не было в предыдущих вариантах
                $query = $group->tasks()
                    ->where('subject_id', $subjectKey)
                    ->with('group');
                
                if (!empty($usedTaskIds)) {
                    $query->whereNotIn('id', $usedTaskIds);
                }
                
                // Принудительно сбрасываем возможное кеширование
                $task = $query->inRandomOrder()->first();
                
                // Если не нашли новое задание (все уже использованы), берём любое
                if (!$task) {
                    $task = $group->tasks()
                        ->where('subject_id', $subjectKey)
                        ->with('group')
                        ->inRandomOrder()
                        ->first();
                }
                
                if ($task) {
                    $usedTaskIds[] = $task->id;
                }
                
                return $task;
            })->filter()->sortBy(function ($task) {
                return (int) $task->group->formatted_title;
            })->unique(function ($task) {
                return (string) $task->group->formatted_title;
            })->values();

            if ($tasks->isEmpty()) {
                return ['variants' => [], 'firstBaseTask' => null];
            }
            $variants[] = $tasks->pluck('id')->all();
        }

        $firstBase = Task::with('group', 'subject')->find($variants[0][0] ?? null);

        return ['variants' => $variants, 'firstBaseTask' => $firstBase];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Task>  $firstVariantTasks
     * @return array<int, array<int>>
     */
    private function buildManualExtraVariants(Subject $subject, \Illuminate\Support\Collection $firstVariantTasks, int $extraCount): array
    {
        if ($extraCount <= 0) {
            return [];
        }

        $groups = $this->formingGroupsCollection($subject);
        if ($groups->isEmpty()) {
            return [];
        }

        $subjectKey = $subject->subject_id;
        $shared = $this->sharedMarkIdsForSubject($subject);
        $byMark = $firstVariantTasks->keyBy('mark');
        
        $usedTaskIds = $firstVariantTasks->pluck('id')->all();

        $out = [];
        for ($i = 0; $i < $extraCount; $i++) {
            $tasks = $groups->map(function ($group) use ($subjectKey, $shared, $byMark, &$usedTaskIds) {
                if (in_array((int) $group->id, $shared, true)) {
                    $t = $byMark->get($group->id);
                    return $t ? Task::with('group')->find($t->id) : null;
                }

                // Избегаем повторов с предыдущими вариантами
                $query = $group->tasks()
                    ->where('subject_id', $subjectKey)
                    ->with('group');
                
                if (!empty($usedTaskIds)) {
                    $query->whereNotIn('id', $usedTaskIds);
                }
                
                $task = $query->inRandomOrder()->first();
                
                // Если не нашли новое задание, берём любое
                if (!$task) {
                    $task = $group->tasks()
                        ->where('subject_id', $subjectKey)
                        ->with('group')
                        ->inRandomOrder()
                        ->first();
                }
                
                if ($task) {
                    $usedTaskIds[] = $task->id;
                }
                
                return $task;
            })->filter()->sortBy(function ($task) {
                return (int) $task->group->formatted_title;
            })->unique(function ($task) {
                return (string) $task->group->formatted_title;
            })->values();

            if ($tasks->isEmpty()) {
                continue;
            }
            $out[] = $tasks->pluck('id')->all();
        }

        return $out;
    }

    public function view(Task $task)
    {

        $baseGroups = $task->subject->groups->where('is_forming', true)->where('question', '=', null);
        $groupedPacks = $this->pickGroupedPacks(
            $task->subject->groups->where('is_forming', true)->where('question', '!=', null)
        );
        $packTitles = $groupedPacks->map(function ($group) {
            return (string) $group->formatted_title;
        })->filter()->unique();

        $groups = $baseGroups->reject(function ($group) use ($packTitles) {
            return $packTitles->contains((string) $group->formatted_title);
        })->concat($groupedPacks);

        $randomTasks = $groups->map(function ($group) use ($task) {
            return $group->tasks()->where('id', $task->id)->first();
            // return $group->tasks()->inRandomOrder()->first();
        })->filter()->unique(function ($task) {
            return (string) $task->group->formatted_title;
        })->values();

        if ($randomTasks->isEmpty()) {
            $randomTasks = collect([$task]);
        } else {
            $randomTasks = $randomTasks->sortBy(function ($task) {
                return (int) $task->group->formatted_title;
            });
        }

        return response()->view('pdf.task', [
            'tasks' => $randomTasks,
            'group' => $task->group,
            'subject' => $task->subject,
            'withAnswers' => false,
        ]);
    }

    public function viewTasks(Request $request, Task $task)
    {
        $groups = $task->subject->groups;

        $randomTasks = $groups->map(function ($group) use ($task) {
            return $group->tasks()->where('id', $task->id)->first();

        })->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        })->values();

        if ($randomTasks->isEmpty()) {
            $randomTasks = collect([$task]);
        }

        $forBank = $request->boolean('bank');

        return response()->view('pdf.task', [
            'task' => $task,
            'tasks' => $randomTasks,
            'group' => $task->group,
            'subject' => $task->subject,
            'withAnswers' => ! $forBank,
            'embedConstructorPreview' => true,
            'embedBankPreview' => $forBank,
            'embedTaskId' => $task->id,
            'questionHtmlMap' => [],
        ]);
    }

    public function exportPdf(Request $request, Task $task)
    {
        $fileName = 'task-'.$task->id.'-'.time().'.pdf';

        $baseGroups = $task->subject->groups->where('is_forming', true)->where('question', '=', null);
        $groupedPacks = $this->pickGroupedPacks(
            $task->subject->groups->where('is_forming', true)->where('question', '!=', null)
        );
        $packTitles = $groupedPacks->map(function ($group) {
            return (string) $group->formatted_title;
        })->filter()->unique();

        $groups = $baseGroups->reject(function ($group) use ($packTitles) {
            return $packTitles->contains((string) $group->formatted_title);
        })->concat($groupedPacks);
        $tasks = $groups->map(function ($group) use ($task) {
            return $group->tasks()->where('id', $task->id)->first();
        })->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        })->unique(function ($task) {
            return (string) $task->group->formatted_title;
        })->values();

        GenerateTaskPdf::dispatch($task->id, $fileName, $tasks->pluck('id')->all());

        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте файл, когда он будет готов.',
            'data' => [
                'download_url' => url('exports/tasks/'.$task->id.'/'.$fileName),
            ],
        ]);
    }

    public function exportPdfManual(Request $request)
    {
        $maxV = (int) config('task_variants.max_per_order', 10);
        $validated = $request->validate([
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*' => ['integer', 'exists:tasks,id'],
            'variant_count' => ['sometimes', 'integer', 'min:1', 'max:'.$maxV],
        ]);
        $variantCount = max(1, min($maxV, (int) ($validated['variant_count'] ?? 1)));
        $user = $request->user();
        // if (! $user || ! $user->isSubscribe()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Для создания вариантов в ручную, вам необходимо приобести платную подписку в которой будет доступен полный функционал',
        //     ]);
        // }
        $taskIds = array_values($validated['tasks']);
        $baseTask = Task::with('subject', 'group')->findOrFail($taskIds[0]);
        $firstTasks = Task::with('group')->whereIn('id', $taskIds)->get();
        $firstTasks = $firstTasks->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        })->values();

        $firstIds = $firstTasks->pluck('id')->all();
        $subjectRow = Subject::where('subject_id', $baseTask->subject_id)->with('groups')->firstOrFail();

        $extraVariants = $this->buildManualExtraVariants($subjectRow, $firstTasks, $variantCount - 1);
        $allVariants = array_merge([$firstIds], $extraVariants);
        
        Log::info('Manual export prepared', [
            'variant_count_requested' => $variantCount,
            'all_variants_count' => count($allVariants),
            'first_ids_count' => count($firstIds),
            'extra_variants_count' => count($extraVariants),
        ]);

        $baseName = $baseTask->subject?->class_name ?? ('subject_'.$baseTask->subject_id);
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $baseName) ?: 'bundle';
        $zipName = $baseName.'.zip';

        $order = Order::create([
            'subject_id' => $subjectRow->id,
            'base_task_id' => $baseTask->id,
            'task_ids' => $firstIds,
            'variant_count' => count($allVariants),
            'variants_task_ids' => count($allVariants) > 1 ? $allVariants : null,
            'zip_file_name' => $zipName,
            'user_id' => $user->id,
            'files_expire_at' => now()->addMinutes(Order::FILES_TTL_MINUTES),
            'files_purged_at' => null,
            'file_url' => '',
        ]);
        $order->refresh();
        $order->forceFill(['file_url' => $order->download_url])->saveQuietly();

        GenerateTaskBundle::dispatch(
            $baseTask->id,
            $firstIds,
            $zipName,
            $order->uuid,
            count($allVariants) > 1 ? $allVariants : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте архив, когда он будет готов.',
            'data' => [
                'variant_uuid' => $order->uuid,
                'download_url' => $order->download_url,
            ],
        ]);
    }

    public function checkReady($taskId, $file)
    {
        $taskId = intval($taskId);
        $file = basename($file);
        $path = public_path('exports/tasks/'.$taskId.'/'.$file);
        $exists = is_file($path) && filesize($path) > 0;

        return response()->json([
            'success' => true,
            'data' => ['ready' => $exists],
        ]);
    }

    public function viewTasksWord(Task $task)
    {

        $task->load('group');
        $inline = function (?string $html) {
            if (! $html) {
                return '';
            }

            return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
                $src = $m[1] ?? '';
                $resolved = $this->resolvePreviewImageSrc($src);

                return str_replace($src, $resolved, $m[0]);
            }, $html);
        };
        $questionHtmlMap = [$task->id => $inline($task->question)];

        return response()->view('word.task', [
            'tasks' => collect([$task]),
            'withAnswers' => true,
            'questionHtmlMap' => $questionHtmlMap,
        ]);
    }

    private function resolvePreviewImageSrc(string $src): string
    {
        if ($src === '' || str_starts_with($src, 'data:')) {
            return $src;
        }
        if (preg_match('#^https?://#i', $src)) {
            return $src;
        }
        $path = ltrim($src, '/');
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }
        $full = public_path($path);
        if (is_file($full)) {
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            $map = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml'];
            $mime = $map[$ext] ?? (@mime_content_type($full) ?: 'image/jpeg');
            $data = @file_get_contents($full);
            if ($data !== false) {
                return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }

        return url('/'.ltrim($path, '/'));
    }

    public function exportPdfAuto(Request $request)
    {
        $maxV = (int) config('task_variants.max_per_order', 10);
        $request->validate([
            'variant_count' => ['sometimes', 'integer', 'min:1', 'max:'.$maxV],
        ]);
        $variantCount = max(1, min($maxV, (int) $request->input('variant_count', 1)));

        $subjectId = $request->input('subject_id') ?? $request->input('task_subject_id');
        $subject = Subject::with('groups')->findOrFail($subjectId);
        $user = $request->user();

        $built = $this->buildAutoVariants($subject, $variantCount);
        $variants = $built['variants'];
        $baseTask = $built['firstBaseTask'];

        if ($variants === [] || ! $baseTask) {
            return response()->json([
                'success' => false,
                'message' => 'Не найдено задач для экспорта.',
            ], 404);
        }

        $firstIds = $variants[0];
        
        Log::info('Auto export prepared', [
            'variant_count_requested' => $variantCount,
            'variants_generated' => count($variants),
            'subject_id' => $subject->subject_id,
            'class_name' => $subject->class_name,
        ]);
        
        $baseName = $baseTask->subject?->class_name ?? ('subject_'.$baseTask->subject_id);
        $baseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $baseName) ?: 'bundle';
        $zipName = $baseName.'.zip';

        $order = Order::create([
            'subject_id' => $subject->id,
            'base_task_id' => $baseTask->id,
            'task_ids' => $firstIds,
            'variant_count' => count($variants),
            'variants_task_ids' => count($variants) > 1 ? $variants : null,
            'zip_file_name' => $zipName,
            'user_id' => $user?->id,
            'files_expire_at' => now()->addMinutes(Order::FILES_TTL_MINUTES),
            'files_purged_at' => null,
            'file_url' => '',
        ]);
        $order->refresh();
        $order->forceFill(['file_url' => $order->download_url])->saveQuietly();

        GenerateTaskBundle::dispatch(
            $baseTask->id,
            $firstIds,
            $zipName,
            $order->uuid,
            count($variants) > 1 ? $variants : null
        );

        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте архив, когда он будет готов.',
            'data' => [
                'variant_uuid' => $order->uuid,
                'download_url' => $order->download_url,
            ],
        ]);
    }

    public function checkReadyAuto($taskId, $file)
    {
        $taskId = intval($taskId);
        $file = basename($file);
        $path = public_path('exports/tasks/'.$taskId.'/'.$file);
        $exists = is_file($path) && filesize($path) > 0;

        return response()->json([
            'success' => true,
            'data' => ['ready' => $exists],
        ]);
    }

    public function downloadVariant(string $uuid): BinaryFileResponse|JsonResponse
    {
        $order = Order::query()->where('uuid', $uuid)->firstOrFail();
        if ($order->retention_expired) {
            $msg = Order::retentionDays() !== null
                ? 'Срок хранения записи варианта в базе истёк.'
                : 'Вариант недоступен.';

            return response()->json([
                'success' => false,
                'message' => $msg,
            ], 410);
        }

        $path = $order->resolveZipAbsolutePath();
        if ($path === null || ! is_file($path) || filesize($path) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Архив ещё не готов. Сначала дождитесь формирования (очередь) или откройте скачивание из личного кабинета.',
                'data' => ['not_ready' => true],
            ], 409);
        }

        return response()->download($path, $order->zip_file_name ?: basename($path));
    }

    /**
     * Поставить генерацию варианта в очередь (как при первом создании). UUID — секрет ссылки.
     */
    public function queueVariantGeneration(Request $request, string $uuid): JsonResponse
    {
        $order = Order::query()->where('uuid', $uuid)->firstOrFail();

        if ($order->retention_expired) {
            return response()->json([
                'success' => false,
                'message' => 'Срок хранения записи варианта истёк.',
            ], 410);
        }

        if ($order->variantsTaskIdsList() === [] || ! $order->base_task_id || ! $order->zip_file_name) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно данных в базе для формирования варианта.',
            ], 422);
        }

        if ($order->files_available) {
            return response()->json([
                'success' => true,
                'message' => 'Архив уже готов.',
                'data' => ['ready' => true],
            ]);
        }

        try {
            return Cache::lock('variant-dispatch:'.$order->uuid, 120)->block(15, function () use ($order) {
                $order->refresh();
                if ($order->files_available) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Архив уже готов.',
                        'data' => ['ready' => true],
                    ]);
                }

                $this->startVariantGenerationJob($order);

                return response()->json([
                    'success' => true,
                    'message' => 'Формирование поставлено в очередь.',
                    'data' => ['queued' => true],
                ], 202);
            });
        } catch (LockTimeoutException) {
            return response()->json([
                'success' => false,
                'message' => 'Запрос на формирование уже обрабатывается. Подождите несколько секунд.',
            ], 429);
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function startVariantGenerationJob(Order $order): void
    {
        $variants = $order->variantsTaskIdsList();
        if ($variants === [] || ! $order->base_task_id || ! $order->zip_file_name) {
            throw new \InvalidArgumentException('Insufficient order data for GenerateTaskBundle.');
        }

        $order->deleteVariantFilesFromDisk();
        $order->forceFill([
            'files_purged_at' => null,
            'files_expire_at' => now()->addMinutes(Order::FILES_TTL_MINUTES),
            'file_url' => $order->download_url,
        ])->saveQuietly();

        $first = $variants[0];
        GenerateTaskBundle::dispatch(
            (int) $order->base_task_id,
            $first,
            (string) $order->zip_file_name,
            $order->uuid,
            count($variants) > 1 ? $variants : null
        );
    }

    public function variantStatus(string $uuid): \Illuminate\Http\JsonResponse
    {
        $order = Order::query()->where('uuid', $uuid)->firstOrFail();
        if ($order->retention_expired) {
            return response()->json([
                'success' => false,
                'message' => 'Срок хранения варианта истёк.',
            ], 410);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ready' => $order->files_available,
            ],
        ]);
    }

    public function regenerateVariant(Request $request, string $uuid): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => false, 'message' => 'Необходима авторизация'], 401);
        }

        $order = Order::query()
            ->where('uuid', $uuid)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($order->retention_expired) {
            $days = Order::retentionDays();

            return response()->json([
                'success' => false,
                'message' => $days !== null
                    ? "Нельзя пересоздать: прошло более {$days} д. с момента формирования."
                    : 'Вариант недоступен.',
            ], 410);
        }

        if ($order->variantsTaskIdsList() === [] || ! $order->base_task_id || ! $order->zip_file_name) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно данных для пересоздания варианта.',
            ], 422);
        }

        try {
            $this->startVariantGenerationJob($order);
        } catch (\InvalidArgumentException) {
            return response()->json([
                'success' => false,
                'message' => 'Недостаточно данных для пересоздания варианта.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Формирование запущено. Скачайте архив, когда он будет готов.',
            'data' => [
                'variant_uuid' => $order->uuid,
                'download_url' => $order->fresh()->download_url,
            ],
        ]);
    }
}
