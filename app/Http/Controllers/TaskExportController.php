<?php
namespace App\Http\Controllers;

use App\Jobs\GenerateTaskPdf;
use App\Jobs\GenerateTaskBundle;
use App\Models\Task;
use Illuminate\Http\Request;

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

        $taskIds = array_values($validated['tasks']);
        $baseTask = Task::findOrFail($taskIds[0]);
        $tasks = Task::whereIn('id', $taskIds)->get();
        $tasks = $tasks->filter()->sortBy(function ($task) {
            return (int) $task->group->formatted_title;
        });
        $zipName = 'bundle-' . $baseTask->id . '-' . time() . '.zip';
        // Новая джоба генерирует 4 файла и упаковывает их в ZIP
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
}
