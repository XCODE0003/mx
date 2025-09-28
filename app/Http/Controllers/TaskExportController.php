<?php
namespace App\Http\Controllers;

use App\Jobs\GenerateTaskPdf;
use App\Jobs\GenerateTaskBundle;
use App\Models\Task;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Order;
class TaskExportController extends Controller
{
    public function view(Task $task)
    {
        $groups = $task->subject->groups;

        $randomTasks = $groups->map(function ($group) use ($task) {
            return $group->tasks()->where('article_id', $task->article_id)->first();
            // return $group->tasks()->inRandomOrder()->first();
        })->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });
        return response()->view('pdf.task', [
            'tasks'    => $randomTasks,
            'group'    => $task->group,
            'subject'  => $task->subject,
            'withAnswers' => true,
        ]);
    }
    public function viewTasks(Task $task)
    {
        $groups = $task->subject->groups;

        $randomTasks = $groups->map(function ($group) use ($task) {
            return $group->tasks()->where('id', $task->id)->first();
            // return $group->tasks()->inRandomOrder()->first();
        })->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });
        return response()->view('pdf.view_task', [
            'task'    => $task,
            'group'    => $task->group,
            'subject'  => $task->subject,
            'withAnswers' => true,
        ]);
    }

    public function exportPdf(Request $request, Task $task)
    {
        $fileName = 'task-' . $task->id . '-' . time() . '.pdf';
        // Запускаем генерацию в очереди, кладём итог в public/exports/tasks/{id}/
        $tasks = $task->subject->groups->map(function ($group) use ($task) {
            return $group->tasks()->where('id', $task->id)->first();
            // return $group->tasks()->inRandomOrder()->first();
        })->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });

        // Передаём базовый $task, имя файла и массив id заданий
        GenerateTaskPdf::dispatch($task->id, $fileName, $tasks->pluck('id')->all());

        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте файл, когда он будет готов.',
            'data'    => [
                'download_url' => url('exports/tasks/' . $task->id . '/' . $fileName),
            ],
        ]);
    }
    public function exportPdfManual(Request $request)
    {
        $validated = $request->validate([
            'tasks' => ['required', 'array', 'min:1'],
            'tasks.*' => ['integer', 'exists:tasks,id'],
        ]);
        $user = $request->user();
        if (!$user || !$user->isSubscribe()) {
            return response()->json([
                'success' => false,
                'message' => 'Для создания вариантов в ручную, вам необходимо приобести платную подписку в которой будет доступен полный функционал',
            ]);
        }
        $taskIds = array_values($validated['tasks']);
        $baseTask = Task::findOrFail($taskIds[0]);
        $tasks = Task::whereIn('id', $taskIds)->get();
        $tasks = $tasks->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });
        $zipName = 'bundle-' . $baseTask->id . '-' . time() . '.zip';
        $order = Order::create([
            'subject_id' => $baseTask->id,
            'task_ids' => json_encode($tasks->pluck('id')->all()),
            'user_id' => $user->id,
            'file_url' => url('exports/tasks/' . $baseTask->id . '/' . $zipName),
        ]);
        GenerateTaskBundle::dispatch($baseTask->id, $tasks->pluck('id')->all(), $zipName);

        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте архив, когда он будет готов.',
            'data'    => [
                'download_url' => url('exports/tasks/' . $baseTask->id . '/' . $zipName),
                'task_id' => $baseTask->id,
                'file' => $zipName,
            ],
        ]);
    }

    public function checkReady($taskId, $file)
    {
        $taskId = intval($taskId);
        $file = basename($file);
        $path = public_path('exports/tasks/' . $taskId . '/' . $file);
        $exists = is_file($path) && filesize($path) > 0;
        return response()->json([
            'success' => true,
            'data' => [ 'ready' => $exists ]
        ]);
    }
    public function viewTasksWord(Task $task)
    {
        // Предпросмотр: показываем текущую задачу в Word-верстке
        $task->load('group');
        $inline = function (?string $html) {
            if (!$html) return '';
            return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
                $src = $m[1] ?? '';
                $resolved = $this->resolvePreviewImageSrc($src);
                return str_replace($src, $resolved, $m[0]);
            }, $html);
        };
        $questionHtmlMap = [ $task->id => $inline($task->question) ];
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
            $map = [ 'jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','gif'=>'image/gif','webp'=>'image/webp','svg'=>'image/svg+xml' ];
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
        $subjectId = $request->input('subject_id') ?? $request->input('task_subject_id');
        $subject = Subject::findOrFail($subjectId);
        $user = $request->user();
        $tasks = Task::where('subject_id', $subject->subject_id)
            ->whereNotNull('mark')
            ->whereNotNull('response')
            ->get()
            ->groupBy('mark')
            ->map(function ($group) {
                return $group->first();
            })
            ->values();

        $tasks = $tasks->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });
        $zipName = 'bundle-' . $tasks->pluck('id')->implode('-') . '-' . time() . '.zip';
        GenerateTaskBundle::dispatch($tasks->first()->id, $tasks->pluck('id')->all(), $zipName);
        $order = Order::create([
            'subject_id' => $subject->id,
            'task_ids' => json_encode($tasks->pluck('id')->all()),
            'user_id' => $user ? $user->id : null,
            'file_url' => url('exports/tasks/' . $tasks->first()->id . '/' . $zipName),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Генерация запущена. Скачайте архив, когда он будет готов.',
            'data'    => [
                'download_url' => url('exports/tasks/' . $tasks->first()->id . '/' . $zipName),
                'task_id' => $tasks->first()->id,
                'file' => $zipName,
            ],
        ]);
    }

    public function checkReadyAuto($taskId, $file)
    {
        $taskId = intval($taskId);
        $file = basename($file);
        $path = public_path('exports/tasks/' . $taskId . '/' . $file);
        $exists = is_file($path) && filesize($path) > 0;
        return response()->json([
            'success' => true,
            'data' => [ 'ready' => $exists ]
        ]);
    }
}
