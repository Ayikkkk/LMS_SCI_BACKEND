<?php

namespace App\Http\Controllers\Student;

use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ExercisePoint;
use App\Models\OnlineMeeting;
use App\Models\Report;
use App\Models\Post;

class DashboardController extends Controller
{
    /**
     * Build photo URL dari APP_URL — konsisten dengan AuthController::photoUrl()
     */
    private function buildPhotoUrl(?string $path, Request $request): ?string
    {
        if (!$path) return null;

        $appUrl = rtrim((string) config('app.url', ''), '/');

        if (empty($appUrl) || str_contains($appUrl, 'localhost') || str_contains($appUrl, '127.0.0.1')) {
            $appUrl = $request->getSchemeAndHttpHost();
        }

        // Legacy: path sudah berupa full URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $relativePath = ltrim(str_replace('/storage/', '', $parsed), '/');
            return $appUrl . '/api/files/' . $relativePath;
        }

        return $appUrl . '/api/files/' . $path;
    }

    public function index(Request $request)
    {
        $student = $request->user();

        // ===============================
        // STATISTIK
        // ===============================

        $totalTasks = Post::where('is_task', 1)
            ->where('serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->count();

        $totalMaterials = Post::where('is_task', 0)
            ->where('serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->count();
        $totalExercises = ExercisePoint::where('student_id', $student->id)->count();
        $avgTask = Task::where('student_id', $student->id)->avg('point') ?? 0;
        $avgExercise = ExercisePoint::where('student_id', $student->id)->avg('exercise_point') ?? 0;
        $reportCount = Report::where('student_id', $student->id)->count();

        // ===============================
        // MEETINGS HARI INI
        // ===============================
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $meetingsToday = OnlineMeeting::query()
            ->where('classroom_id', $student->classroom_id)
            ->whereBetween('start_time', [$todayStart, $todayEnd])
            ->orderBy('start_time', 'asc')
            ->get([
                'id',
                'title',
                'start_time',
                'end_time',
                'status'
            ])
            ->map(function ($meeting) {
                $now = now();

                $start = $meeting->start_time ? \Carbon\Carbon::parse($meeting->start_time) : null;
                $end   = $meeting->end_time ? \Carbon\Carbon::parse($meeting->end_time) : null;

                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'platform' => 'Jitsi Meet',
                    // Kirim sebagai ISO 8601 dengan timezone WIB eksplisit
                    'start_time' => $meeting->start_time
                        ? \Carbon\Carbon::parse($meeting->start_time)
                            ->setTimezone('Asia/Jakarta')
                            ->toIso8601String()
                        : null,
                    'end_time' => $meeting->end_time
                        ? \Carbon\Carbon::parse($meeting->end_time)
                            ->setTimezone('Asia/Jakarta')
                            ->toIso8601String()
                        : null,
                    'status' => $meeting->status,

                    //  SAFE CHECK
                    'is_live' => $start && $end ? $now->between($start, $end) : false,
                    'is_upcoming' => $start ? $start->gt($now) : false,
                ];
            });

        // ===============================
        // PENDING TASKS (Belum dikerjakan)
        // ===============================

        $pendingTasks = DB::table('posts')
            ->leftJoin('tasks', function ($join) use ($student) {
                $join->on('posts.id', '=', 'tasks.post_id')
                    ->where('tasks.student_id', $student->id);
            })
            ->leftJoin('mapels', 'posts.mapel_id', '=', 'mapels.id')
            ->where('posts.is_task', 1)
            ->where('posts.serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('posts.classroom_id')
                  ->orWhere('posts.classroom_id', $student->classroom_id);
            })
            ->whereNull('tasks.id')
            ->where(function ($q) {
                $q->whereNull('posts.due_date')
                  ->orWhereDate('posts.due_date', '>=', now());
            })
            ->select(
                'posts.id',
                'posts.title',
                'posts.due_date',
                'mapels.name as subject_name'
            )
            ->orderByRaw('CASE WHEN posts.due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('posts.due_date', 'asc')
            ->get();

        // ===============================
        // RESPONSE
        // ===============================

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id'        => $student->id,
                    'name'      => $student->name,
                    'username'  => $student->username,
                    'email'     => $student->email,
                    'className' => optional($student->classroom)->name,
                    'photo'     => $this->buildPhotoUrl($student->photo, $request),
                ],
                'stats' => [
                    'total_tasks'            => $totalTasks,
                    'total_materials'        => $totalMaterials,
                    'total_exercises'        => $totalExercises,
                    'average_task_score'     => round($avgTask, 2),
                    'average_exercise_score' => round($avgExercise, 2),
                    'report_count'           => $reportCount,
                ],
                'meetings_today' => $meetingsToday,
                // ⬇️ kirim tugas yang belum selesai
                'pending_tasks' => $pendingTasks,
            ],
        ]);
    }
}
