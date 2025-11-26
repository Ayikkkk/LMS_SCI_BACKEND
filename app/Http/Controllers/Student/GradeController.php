<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ExercisePoint;

class GradeController extends Controller
{
    /**
     * 📘 Nilai semua tugas siswa
     */
    public function taskGrades(Request $request)
    {
        $student = $request->user();

        $tasks = Task::with('post')
            ->where('student_id', $student->id)
            ->orderBy('id', 'desc')
            ->get(['id', 'post_id', 'description', 'point', 'attachment', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     * 🧮 Nilai semua latihan/quiz siswa
     */
    public function exerciseGrades(Request $request)
    {
        $student = $request->user();

        $exercises = ExercisePoint::with('exercise')
            ->where('student_id', $student->id)
            ->orderBy('id', 'desc')
            ->get(['id', 'exercise_id', 'exercise_point', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    /**
     * 📊 Rekap nilai gabungan (tugas + latihan)
     */
    public function summary(Request $request)
    {
        $student = $request->user();

        $avgTasks = Task::where('student_id', $student->id)->avg('point');
        $avgExercises = ExercisePoint::where('student_id', $student->id)->avg('exercise_point');

        $summary = [
            'average_tasks' => round($avgTasks ?? 0, 2),
            'average_exercises' => round($avgExercises ?? 0, 2),
            'final_score' => round(((($avgTasks ?? 0) + ($avgExercises ?? 0)) / 2), 2)
        ];

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
}
