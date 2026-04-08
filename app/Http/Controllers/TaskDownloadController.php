<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateTaskBundle;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskDownloadController extends Controller
{
    public function prepare(Task $task)
    {
        $zipFileName = 'task_' . $task->id . '_' . time() . '.zip';
        
        // Запускаем джобу для генерации
        GenerateTaskBundle::dispatch(
            $task->id,
            [$task->id],
            $zipFileName,
            null,
            null
        );
        
        return Inertia::render('Admin/TaskDownload', [
            'taskId' => $task->id,
            'zipFileName' => $zipFileName,
        ]);
    }
    
    public function checkStatus(Task $task, Request $request)
    {
        $fileName = $request->query('file');
        $path = public_path('exports/tasks/' . $task->id . '/' . $fileName);
        
        $ready = is_file($path) && filesize($path) > 0;
        
        return response()->json([
            'success' => true,
            'ready' => $ready,
            'downloadUrl' => $ready ? url('exports/tasks/' . $task->id . '/' . $fileName) : null,
        ]);
    }
}
