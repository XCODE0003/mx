<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskPreviewController extends Controller
{
    public function show(Task $task)
    {
        $task->load(['subject', 'blankText']);

        return view('pdf.task', [
            'tasks' => collect([$task]),
            'subject' => $task->subject,
            'showAnswers' => true,
            'embedConstructorPreview' => true,
        ]);
    }
}
