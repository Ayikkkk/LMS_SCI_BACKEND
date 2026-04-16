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
    public function index(Request $request)
    {
        $student = $request->user();

        // ===============================
        // STATISTIK
        // ===============================

        $totalTasks = Post::where('is_task', 1)
            ->where('serial_id', $student->serial_id)
            ->count();
        $totalMaterials = Post::where('is_task', 0)
            ->where('serial_id', $student->serial_id)
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
                    'start_time' => $meeting->start_time,
                    'end_time' => $meeting->end_time,
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
            ->where('posts.is_task', 1)
            ->where('posts.serial_id', $student->serial_id)
            ->whereNull('tasks.id')
            ->whereDate('posts.due_date', '>=', now())
            ->select(
                'posts.id',
                'posts.title',
                'posts.due_date'
            )
            ->orderBy('posts.due_date', 'asc')
            ->get();

        // ===============================
        // RESPONSE
        // ===============================

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'email' => $student->email,
                    'className' => optional($student->classroom)->name,
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
