<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExercisePoint;

class ExerciseController extends Controller
{
    // 📄 Daftar semua latihan/quiz
    public function index(Request $request)
    {
        $student = $request->user();

        $exercises = Exercise::with('lesson')
            ->where('serial_id', $student->serial_id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    // 🔍 Detail quiz & soal
    public function show($id)
    {
        $exercise = Exercise::with('items')->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $exercise
        ]);
    }

    // 📝 Kirim jawaban quiz
    public function submit(Request $request, $id)
    {
        $student = $request->user();

        $request->validate([
            'answers' => 'required|array', // format: [{"question_id":1,"answer":"A"}]
        ]);

        $exercise = Exercise::find($id);
        if (!$exercise) {
            return response()->json(['message' => 'Latihan tidak ditemukan'], 404);
        }

        $totalQuestions = count($request->answers);
        $correctAnswers = 0;

        foreach ($request->answers as $ans) {
            $question = ExerciseItem::find($ans['question_id']);

            if ($question && strtolower(trim($question->answer)) == strtolower(trim($ans['answer']))) {
                $correctAnswers++;
            }
        }

        $score = round(($correctAnswers / $totalQuestions) * 100, 2);

        $point = ExercisePoint::create([
            'serial_id' => $student->serial_id,
            'exercise_id' => $exercise->id,
            'student_id' => $student->id,
            'answer' => json_encode($request->answers),
            'exercise_point' => $score,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jawaban berhasil dikirim',
            'score' => $score,
            'data' => $point
        ]);
    }

    // 📊 Lihat hasil / nilai quiz
    public function result(Request $request, $id)
    {
        $student = $request->user();

        $result = ExercisePoint::where('exercise_id', $id)
            ->where('student_id', $student->id)
            ->latest()
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada hasil untuk latihan ini'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
