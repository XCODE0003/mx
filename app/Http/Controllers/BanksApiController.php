<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Subject;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BanksApiController extends Controller
{
    public function subjects(string $grade): JsonResponse
    {
        $subjects = Subject::query()
            ->where('exam_type', $grade)
            ->whereHas('groups')
            ->orderByDesc('name')
            ->get();

        return response()->json($subjects);
    }

    public function groups(string $grade, string $subject): JsonResponse
    {
        $subjectModel = Subject::query()
            ->where('exam_type', $grade)
            ->where(function ($q) use ($subject) {
                $q->where('subject_id', (string) $subject);
                if (ctype_digit((string) $subject)) {
                    $q->orWhere('id', (int) $subject);
                }
            })
            ->first();

        if (! $subjectModel) {
            return response()->json(['groups' => [], 'total' => 0]);
        }

        $groups = Group::query()
            ->where('subject_id', $subjectModel->subject_id)
            ->withCount('tasks')
            ->get()
            ->sort(function ($a, $b) {
                $normalize = function ($s) {
                    $s = str_replace(['–', '—', '−'], '-', $s);
                    $s = str_replace(["\u{00A0}", "\u{202F}"], ' ', $s);

                    return trim($s);
                };
                $parse = function ($title) use ($normalize) {
                    $t = $normalize($title);
                    if (preg_match_all('/\d+/', $t, $nums) && isset($nums[0][0])) {
                        $start = (int) $nums[0][0];
                        $end = isset($nums[0][1]) ? (int) $nums[0][1] : $start;

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
            })
            ->values();

        return response()->json([
            'groups' => $groups,
            'total' => $groups->sum('tasks_count'),
        ]);
    }

    public function tasks(Request $request, int $group_id): JsonResponse
    {
        $group = Group::query()->where('id', $group_id)->first();
        if (! $group) {
            return response()->json([
                'tasks' => [],
                'total' => 0,
                'page' => 1,
                'per_page' => 5,
                'last_page' => 1,
            ]);
        }

        $perPage = min(50, max(1, (int) $request->query('per_page', 5)));
        $page = max(1, (int) $request->query('page', 1));

        $paginator = Task::query()
            ->where('mark', $group->id)
            ->where('subject_id', $group->subject_id)
            ->select([
                'id',
                'question',
                'image',
                'article_id',
                'response',
                'type_answer',
                'table_answer',
                'additional_files',
                'mark',
            ])
            ->orderBy('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'tasks' => $paginator->items(),
            'total' => $paginator->total(),
            'page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'last_page' => $paginator->lastPage(),
        ]);
    }
}
