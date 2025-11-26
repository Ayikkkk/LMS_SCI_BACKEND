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

        // Jumlah tugas yang sudah dikumpulkan
        $totalTasks = Task::where('student_id', $student->id)->count();

        // Jumlah latihan (quiz) yang sudah dikerjakan
        $totalExercises = ExercisePoint::where('student_id', $student->id)->count();

        // Nilai rata-rata
        $avgTask = Task::where('student_id', $student->id)->avg('point');
        $avgExercise = ExercisePoint::where('student_id', $student->id)->avg('exercise_point');

        // Jumlah laporan harian
        $reportCount = Report::where('student_id', $student->id)->count();

        // Meeting hari ini
        $today = now()->format('Y-m-d');
        $meetingsToday = OnlineMeeting::where('classroom_id', $student->classroom_id)
            ->whereDate('start_time', $today)
            ->orderBy('start_time', 'asc')
            ->get(['id', 'title', 'platform', 'meeting_link', 'start_time', 'end_time', 'status']);

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'username' => $student->username,
                    'email' => $student->email,
                    'classroom_name' => $student->classroom->name,
                ],
                'stats' => [
                    'total_tasks' => $totalTasks,
                    'total_exercises' => $totalExercises,
                    'average_task_score' => round($avgTask ?? 0, 2),
                    'average_exercise_score' => round($avgExercise ?? 0, 2),
                    'report_count' => $reportCount,
                ],
                'meetings_today' => $meetingsToday,
            ]
        ]);
    }
}
