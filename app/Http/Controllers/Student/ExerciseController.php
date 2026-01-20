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
    //Daftar semua lesson (mapel) yang punya exercise untuk student
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
                ->filter()
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

    //  Daftar exercise untuk lesson tertentu, optional filter by type_id
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
    public function submit(Request $request, $id)
    {
        $student = $request->user();

        // 🔒 Cegah pengerjaan ulang
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
            'answers' => 'required|array',
        ]);

        $answers = $validated['answers'];
        $isAuto = $request->boolean('auto_submit', false); // ✅ tangkap flag auto
        $localScore = $request->input('local_score'); // ✅ jika dikirim dari Flutter

        // ✅ Ambil semua soal quiz dari database
        $questions = ExerciseItem::where('exercise_id', $id)->get();
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada soal untuk latihan ini'
            ], 400);
        }

        $correctAnswers = 0;

        foreach ($questions as $question) {
            $qid = (string) $question->id;

            // Kalau siswa tidak jawab pertanyaan ini, lewati
            if (!isset($answers[$qid])) {
                continue;
            }

            $studentAnswer = strtolower(trim($answers[$qid]));
            $correctAnswer = strtolower(trim($question->answer));

            // Normalisasi berbagai format jawaban benar
            if (str_contains($correctAnswer, 'option_')) {
                $correctAnswer = str_replace('option_', '', $correctAnswer);
            }

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

        // 🧩 Hitung skor
        if ($correctAnswers === 0) {
            $score = 0;
        } else {
            $score = round(($correctAnswers / $totalQuestions) * 100);
        }

        // 🧩 Jika auto submit + local_score dikirim dari Flutter, pakai itu
        if ($isAuto && $localScore !== null) {
            $score = (int) $localScore;
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
            'auto_submit' => $isAuto,
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
