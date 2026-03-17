<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\Browsershot\Browsershot;

class GenerateTaskPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $baseTaskId;
    public string $fileName;
    /** @var array<int> */
    public array $taskIds;

    public ?Task $task = null;
    public ?Collection $tasks = null;

    public $timeout = 300; // seconds
    public $tries = 1;

    public function __construct(int $baseTaskId, string $fileName, array $taskIds = [])
    {
        $this->baseTaskId = $baseTaskId;
        $this->fileName = $fileName;
        $this->taskIds = array_values(array_unique(array_map('intval', $taskIds)));
    }

    public function handle(): void
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);


        if (empty($this->baseTaskId) && $this->task instanceof Task) {
            $this->baseTaskId = (int) $this->task->id;
        }
        $baseTask = Task::with('group', 'subject')->findOrFail($this->baseTaskId);


        if ($this->tasks instanceof Collection && $this->tasks->isNotEmpty()) {
            $tasks = $this->tasks->load('group');
        } else {
            $ids = collect($this->taskIds);
            if ($ids->isEmpty()) {
                $ids = collect([$this->baseTaskId]);
            }
            $tasks = Task::with('group')->whereIn('id', $ids->all())->get();
        }
        $questionHtmlMap = [];
        foreach ($tasks as $t) {
            $questionHtmlMap[$t->id] = $this->inlineImages($t->question);
        }


        $withAnswers = false;

        $html = view('pdf.task', [
            'task' => $baseTask,
            'tasks' => $tasks,
            'subject' => $baseTask->subject,
            'questionHtmlMap' => $questionHtmlMap,
            'withAnswers' => $withAnswers,
        ])->render();

        $dir = public_path('exports/tasks/'.$this->baseTaskId);
        if (! is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $fullPath = $dir.DIRECTORY_SEPARATOR.$this->fileName;

        $browser = Browsershot::html($html)
            ->format('A4')
            ->margins(15, 15, 20, 15)
            ->setDelay(1000)
            ->waitUntilNetworkIdle()
            ->setDelay(5000)
            ->timeout(300)
            ->noSandbox();


        $browser->setOption('args', [
            '--allow-file-access-from-files',
            '--disable-web-security',
        ]);

        $browser->savePdf($fullPath);
    }

    private function inlineImages(?string $html): string
    {
        if (!$html) return '';
        return preg_replace_callback('/<img[^>]*src=["\']([^"\']+)["\'][^>]*>/i', function ($m) {
            $src = $m[1] ?? '';
            $resolved = $this->resolveImageSrc($src);
            return str_replace($src, $resolved, $m[0]);
        }, $html);
    }

    private function resolveImageSrc(string $src): string
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
        if (!is_file($full)) {

            $try = public_path('docs/'.ltrim($path, '/'));
            if (is_file($try)) {
                $full = $try;
            }
        }
        if (is_file($full)) {
            $ext = strtolower(pathinfo($full, PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? (@mime_content_type($full) ?: 'image/jpeg');
            $data = @file_get_contents($full);
            if ($data !== false) {
                return 'data:'.$mime.';base64,'.base64_encode($data);
            }
        }

        $abs = url('/'.ltrim($path, '/'));
        $httpData = @file_get_contents($abs);
        if ($httpData !== false) {
            $ext = strtolower(pathinfo(parse_url($abs, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
            $map = [
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
            ];
            $mime = $map[$ext] ?? 'image/jpeg';
            return 'data:'.$mime.';base64,'.base64_encode($httpData);
        }

        return $abs;
    }
}


