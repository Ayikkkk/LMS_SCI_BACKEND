<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExercisePoint;
use App\Models\Lesson;
use App\Models\ExerciseType;

class ExerciseController extends Controller
{
    // 📄 Daftar semua lesson (mapel) yang punya exercise untuk student
    public function index(Request $request)
    {
        $student = $request->user();

        // Ambil lesson yang punya exercises untuk serial_id siswa
        $lessons = Lesson::whereHas('exercises', function ($q) use ($student) {
            $q->where('serial_id', $student->serial_id);
        })
            ->with(['exercises' => function ($q) use ($student) {
                // hanya exercises untuk serial siswa, include tipe-nya
                $q->where('serial_id', $student->serial_id)
                    ->with('exerciseType');
            }])
            ->orderBy('name')
            ->get();

        // Transform menjadi bentuk ringkas: lesson + list tipe exercise tersedia (dengan count)
        $data = $lessons->map(function ($lesson) {
            $types = $lesson->exercises
                ->groupBy(function ($ex) {
                    return $ex->exerciseType ? $ex->exerciseType->id : null;
                })
                ->map(function ($group, $key) {
                    if ($key === null) {
                        return null;
                    }
                    $type = $group->first()->exerciseType;
                    return [
                        'id' => $type->id,
                        'kode' => $type->kode,
                        'name' => $type->name,
                        'count' => $group->count(),
                    ];
                })
                ->filter() // hapus null
                ->values();

            return [
                'id' => $lesson->id,
                'mapel_id' => $lesson->mapel_id,
                'name' => $lesson->name,
                'grade' => $lesson->grade,
                'semester' => $lesson->semester,
                'category' => $lesson->category,
                'types' => $types,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    // 📄 Daftar exercise untuk lesson tertentu, optional filter by type_id
    public function exercisesByLesson(Request $request, $lessonId)
    {
        $student = $request->user();
        $typeId = $request->query('type_id');

        $query = Exercise::with('exerciseType')
            ->where('lesson_id', $lessonId)
            ->where('serial_id', $student->serial_id);

        if ($typeId) {
            $query->where('exercise_type_id', $typeId);
        }

        $exercises = $query->orderBy('id', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $exercises
        ]);
    }

    // Detail quiz & soal (tidak berubah)
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

    // Kirim jawaban quiz
    // 📝 Kirim jawaban quiz (FINAL - TANPA SIMPAN WAKTU)
    // 📝 Kirim jawaban quiz (FINAL & BENAR)
    public function submit(Request $request, $id)
    {
        $student = $request->user();

        // 🔒 CEK SUDAH PERNAH MENGERJAKAN
        $existing = ExercisePoint::where('exercise_id', $id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Quiz sudah pernah dikerjakan',
                'data' => $existing
            ], 403);
        }

        $validated = $request->validate([
            'answers' => 'required|array', // {question_id: option}
        ]);

        $answers = $validated['answers'];
        $totalQuestions = count($answers);
        $correctAnswers = 0;

        foreach ($answers as $questionId => $selectedOption) {
            $question = ExerciseItem::find($questionId);
            if (!$question) {
                continue;
            }

            // ===== NORMALISASI JAWABAN SISWA =====
            $studentAnswer = strtolower(trim($selectedOption)); // a,b,c,d

            // ===== NORMALISASI JAWABAN BENAR =====
            $correctAnswer = strtolower(trim($question->answer));

            // jika bentuk "option_a"
            if (str_contains($correctAnswer, 'option_')) {
                $correctAnswer = str_replace('option_', '', $correctAnswer);
            }

            // jika JSON ["a"]
            if (str_starts_with($correctAnswer, '[')) {
                try {
                    $parsed = json_decode($correctAnswer, true);
                    if (is_array($parsed) && count($parsed) > 0) {
                        $correctAnswer = strtolower(trim($parsed[0]));
                    }
                } catch (\Exception $e) {
                    // ignore
                }
            }

            if ($studentAnswer === $correctAnswer) {
                $correctAnswers++;
            }
        }

        // ===== HITUNG NILAI =====
        if ($totalQuestions === 0) {
            $score = 0;
        } elseif ($correctAnswers === $totalQuestions) {
            $score = 100;
        } else {
            $score = round(($correctAnswers / $totalQuestions) * 100);
        }

        $point = ExercisePoint::create([
            'serial_id' => $student->serial_id,
            'exercise_id' => $id,
            'student_id' => $student->id,
            'answer' => json_encode($answers),
            'exercise_point' => $score,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Quiz berhasil dikirim',
            'score' => $score,
            'data' => $point
        ]);
    }

    // Lihat hasil / nilai quiz (tetap sama)
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
