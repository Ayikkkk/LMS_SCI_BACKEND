<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ExercisePoint;
use App\Models\OnlineMeeting;
use App\Models\Report;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        // ===============================
        // STATISTIK
        // ===============================

        $totalTasks = Task::where('student_id', $student->id)->count();

        $totalExercises = ExercisePoint::where('student_id', $student->id)->count();

        $avgTask = Task::where('student_id', $student->id)->avg('point') ?? 0;

        $avgExercise = ExercisePoint::where('student_id', $student->id)
            ->avg('exercise_point') ?? 0;

        $reportCount = Report::where('student_id', $student->id)->count();

        // ===============================
        // MEETING HARI INI
        // ===============================

        $meetingsToday = OnlineMeeting::query()
            ->where('classroom_id', $student->classroom_id)
            ->whereDate('start_time', now()->toDateString())
            ->orderBy('start_time', 'asc')
            ->get([
                'id',
                'title',
                'start_time',
                'end_time',
                'status',
            ])
            ->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'platform' => 'Jitsi Meet', // 🔥 LOGIC, BUKAN DB
                    'start_time' => $meeting->start_time,
                    'end_time' => $meeting->end_time,
                    'status' => $meeting->status,
                ];
            });

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
                    'total_tasks' => $totalTasks,
                    'total_exercises' => $totalExercises,
                    'average_task_score' => round($avgTask, 2),
                    'average_exercise_score' => round($avgExercise, 2),
                    'report_count' => $reportCount,
                ],
                'meetings_today' => $meetingsToday,
            ],
        ]);
    }
}
