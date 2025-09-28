<?php

use App\Models\Group;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Routing\Route as RoutingRoute;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group(['prefix' => 'profile', 'middleware' => 'auth'], function () {
    Route::get('/', function () {
        return Inertia::render('Profile/Account');
    })->name('profile.account');

    Route::get('/constructor', function () {
        return Inertia::render('Profile/Constructor');
    })->name('profile.constructor');
    Route::get('/constructor/manual', function () {
        return Inertia::render('Profile/ConstructorManual');
    })->name('profile.constructor.manual');

    Route::get('/history', function () {
        return Inertia::render('Profile/History');
    })->name('profile.history');

    Route::get('/logout', function () {
        Auth::logout();
        return redirect()->route('login');
    })->name('profile.logout');

    Route::get('/constructor/{grade}/{subject}/{group}', function ($grade, $subject, $group) {
        $group = Group::where('subject_id', $subject)->where('exam_type', $grade)->where('name', $group)->first();
        return response()->json($group);
    })->name('profile.constructor.manual.create');
    Route::get('/groups/{grade}/{subject}', function ($grade, $subject) {
        $subject = Subject::where('id', $subject)->where('exam_type', $grade)->first();
        if (!$subject) {
            return response()->json([]);
        }
        $groups = Group::where('subject_id', $subject->subject_id)
            ->withCount('tasks')
            ->get()
            ->sort(function ($a, $b) {
                $normalize = function ($s) {
                    // Replace different dashes with '-'; replace non-breaking spaces with normal space
                    $s = str_replace(["–", "—", "−"], "-", $s);
                    $s = str_replace(["\u{00A0}", "\u{202F}"], ' ', $s);
                    return trim($s);
                };
                $parse = function ($title) use ($normalize) {
                    $t = $normalize($title);
                    if (preg_match_all('/\d+/', $t, $nums) && isset($nums[0][0])) {
                        $start = intval($nums[0][0]);
                        $end = isset($nums[0][1]) ? intval($nums[0][1]) : $start;
                        return [$start, $end];
                    }
                    return [PHP_INT_MAX, PHP_INT_MAX];
                };
                [$aStart, $aEnd] = $parse($a->title);
                [$bStart, $bEnd] = $parse($b->title);
                if ($aStart === $bStart) {
                    return $aEnd <=> $bEnd;
                }
                return $aStart <=> $bStart;
            })->values();

        return response()->json([
            'groups' => $groups,
            'total' => $groups->sum('tasks_count'),
        ]);
    })->name('profile.constructor.manual.groups');
    Route::get('/tasks/{group_id}', function ($group_id) {
        $group = Group::where('id', $group_id)->first();
        if (!$group) {
            return response()->json([]);
        }
        $tasks = Task::where('mark', $group->id)
            ->select('id', 'question', 'image', 'article_id', 'response')
            ->get();
        return response()->json([
            'tasks' => $tasks,
            'total' => $tasks->count(),
        ]);
    })->name('profile.constructor.manual.tasks');
    Route::get('/subjects/{grade}', function ($grade) {
        $subjects = Subject::where('exam_type', $grade)
            ->whereHas('groups')
            ->orderBy('name', 'desc')
            ->get();
        return response()->json($subjects);
    })->name('profile.subjects');
});