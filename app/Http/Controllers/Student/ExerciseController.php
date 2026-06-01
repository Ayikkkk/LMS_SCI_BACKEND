<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExercisePoint;
use App\Models\Lesson;
use App\Models\ExerciseType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExerciseController extends Controller
{
    /**
     * Exercise Model ID Mapping:
     * 1 = Multiple Choice (single answer, radio buttons)
     * 2 = Multiple Answer (multiple answers, checkboxes)
     * 3 = Statement (true/false)
     * 4 = Fill in the Blank
     * 5 = Essay
     * 6 = Yes/No
     * 7 = Argument
     */

    // Daftar semua lesson (mapel) yang punya exercise untuk student
    public function index(Request $request)
    {
        $student = $request->user();

        $lessons = Lesson::whereHas('exercises', function ($q) use ($student) {
            $q->where('serial_id', $student->serial_id);
        })->with(['exercises' => function ($q) use ($student) {
            $q->where('serial_id', $student->serial_id)->with('exerciseType');
        }])->orderBy('name')->get();

        $data = $lessons->map(function ($lesson) {
            $types = $lesson->exercises->groupBy(function ($ex) {
                return $ex->exerciseType ? $ex->exerciseType->id : null;
            })->map(function ($group, $key) {
                if ($key === null) return null;
                $type = $group->first()->exerciseType;
                return [
                    'id' => $type->id,
                    'kode' => $type->kode,
                    'name' => $type->name,
                    'count' => $group->count(),
                ];
            })->filter()->values();

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

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Daftar exercise untuk lesson tertentu
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

        // Ambil semua hasil kuis siswa untuk exercise di lesson ini sekaligus
        $exerciseIds = $exercises->pluck('id');
        $points = ExercisePoint::where('student_id', $student->id)
            ->whereIn('exercise_id', $exerciseIds)
            ->get()
            ->keyBy('exercise_id');

        $data = $exercises->map(function ($ex) use ($points) {
            $point = $points->get($ex->id);
            return [
                'id'            => $ex->id,
                'lesson_id'     => $ex->lesson_id,
                'serial_id'     => $ex->serial_id,
                'exercise_type_id' => $ex->exercise_type_id,
                'title'         => $ex->title,
                'is_admin'      => $ex->is_admin,
                'created_at'    => $ex->created_at,
                'updated_at'    => $ex->updated_at,
                'deleted_at'    => $ex->deleted_at,
                'exercise_type' => $ex->exerciseType,
                // Status pengerjaan
                'is_done'       => $point !== null,
                'score'         => $point ? $point->exercise_point : null,
                'is_pending_review' => $point && $point->exercise_point === null,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ✅ Detail quiz & soal
    public function show($id)
    {
        $exercise = Exercise::with(['items', 'exerciseType'])->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan'
            ], 404);
        }

    // Format items dengan tipe soal yang benar, lalu acak urutan
    $formattedItems = $exercise->items->map(function ($item) {
            $question = strip_tags($item->question);
            $options = $this->parseOptions($item->selection, $item->exercise_model_id);

            return [
                'id' => $item->id,
                'question' => $question,
                'options' => $options,
                'type' => $this->mapModelIdToType($item->exercise_model_id),
                'is_multiple' => $item->exercise_model_id == 2,
                'allow_multiple' => $item->exercise_model_id == 2,
                'multiple_correct' => $item->exercise_model_id == 2,
                'exercise_model_id' => $item->exercise_model_id,
                'exercise_choice' => $item->exercise_choice,
            ];
        })
        ->shuffle() // acak urutan soal setiap request
        ->values();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $exercise->id,
                'title' => $exercise->title ?? 'Quiz',
                'description' => $exercise->description ?? '',
                'time_limit' => $exercise->time_limit ?? null,
                'items' => $formattedItems,
                'exercise_type_name' => $exercise->exerciseType->name ?? null,
                'type_name' => $exercise->exerciseType->name ?? null,
            ]
        ]);
    }

    // ✅ Parse options dari kolom 'selection'
    private function parseOptions($selection, $modelId)
    {
        if (in_array($modelId, [4, 5, 7])) {
            return [];
        }

        if (empty($selection)) {
            return [];
        }

        $options = [];

        if (str_starts_with(trim($selection), '[') || str_starts_with(trim($selection), '{')) {
            try {
                $decoded = json_decode($selection, true);
                if (is_array($decoded)) {
                    $options = array_map(function ($opt) {
                        return strip_tags($opt);
                    }, $decoded);
                    return array_values($options);
                }
            } catch (\Exception $e) {
                // Continue to manual parsing
            }
        }

        if (strpos($selection, "\n") !== false) {
            $options = array_map('trim', explode("\n", $selection));
        } else {
            $options = array_map('trim', explode(',', $selection));
        }

        $options = array_map(function ($opt) {
            return strip_tags($opt);
        }, $options);

        $options = array_filter($options, function ($opt) {
            return !empty($opt);
        });

        return array_values($options);
    }

    // ✅ Map exercise_model_id ke tipe Flutter
    private function mapModelIdToType($modelId)
    {
        $mapping = [
            1 => 'multiple_choice',
            2 => 'multiple_answer',
            3 => 'true_false',
            4 => 'fill_in_the_blank',
            5 => 'essay',
            6 => 'yes_no',
            7 => 'essay',
        ];

        return $mapping[$modelId] ?? 'multiple_choice';
    }

    // ✅ Cek apakah tipe exercise perlu penilaian manual
    private function isManualGradingType($typeName)
    {
        if (empty($typeName)) {
            return false;
        }

        $typeName = strtolower(trim($typeName));

        // Daftar tipe yang perlu penilaian manual
        $manualGradingTypes = [
            'akm',
            'asesmen kompetensi minimum',
            'essay',
            'uraian',
            'project',
            'proyek',
        ];

        // Cek exact match
        if (in_array($typeName, $manualGradingTypes)) {
            return true;
        }

        // Cek partial match (jika nama mengandung kata kunci)
        foreach ($manualGradingTypes as $type) {
            if (strpos($typeName, $type) !== false) {
                return true;
            }
        }

        return false;
    }

    // ✅ Submit quiz - CORRECTED (bagian awal yang hilang sudah ditambahkan)
    public function submit(Request $request, $id)
    {
        $student = $request->user();

        // ✅ BAGIAN INI YANG HILANG DI CONTROLLER ANDA!
        // Cegah pengerjaan ulang
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

        $isAuto = $request->boolean('auto_submit', false);

        $validated = $request->validate([
            // Saat auto_submit (waktu habis), answers boleh kosong
            'answers' => $isAuto ? 'nullable|array' : 'required|array',
        ]);

        $answers = $validated['answers'] ?? [];
        $localScore = $request->input('local_score');

        // ✅ AMBIL INFO EXERCISE TYPE
        $exercise = Exercise::with('exerciseType')->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise tidak ditemukan'
            ], 404);
        }

        $exerciseTypeName = $exercise->exerciseType->name ?? '';

        // ✅ CEK APAKAH PERLU MANUAL GRADING
        $isPendingReview = $this->isManualGradingType($exerciseTypeName);

        $questions = ExerciseItem::where('exercise_id', $id)->get();
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada soal untuk latihan ini'
            ], 400);
        }

        $score = null;

        // ✅ HANYA HITUNG SKOR JIKA BUKAN MANUAL GRADING
        if (!$isPendingReview) {
            $correctAnswers = 0;

            foreach ($questions as $question) {
                $qid = (string) $question->id;

                if (!isset($answers[$qid])) {
                    continue;
                }

                $studentAnswer = $answers[$qid];
                $correctAnswer = trim($question->answer);

                switch ($question->exercise_model_id) {
                    case 2:
                        $correctAnswers += $this->checkMultipleAnswer($studentAnswer, $correctAnswer);
                        break;
                    case 4:
                    case 5:
                    case 7:
                        $correctAnswers += $this->checkTextAnswer($studentAnswer, $correctAnswer);
                        break;
                    default:
                        $correctAnswers += $this->checkSingleAnswer($studentAnswer, $correctAnswer);
                        break;
                }
            }

            $score = $correctAnswers === 0 ? 0 : round(($correctAnswers / $totalQuestions) * 100);

            if ($isAuto && $localScore !== null) {
                $score = (int) $localScore;
            }
        }

        // ✅ SIMPAN KE DATABASE (wrapped in transaction to prevent partial saves)
        $point = DB::transaction(function () use ($student, $id, $answers, $score) {
            return ExercisePoint::create([
                'serial_id'      => $student->serial_id,
                'exercise_id'    => $id,
                'student_id'     => $student->id,
                'answer'         => json_encode($answers),
                'exercise_point' => $score,
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => $isPendingReview
                ? 'Quiz berhasil dikirim, menunggu penilaian guru'
                : 'Quiz berhasil dikirim',
            'score' => $score,
            'auto_submit' => $isAuto,
            'is_pending_review' => $isPendingReview,
            'data' => $point
        ]);
    }

    private function checkSingleAnswer($studentAnswer, $correctAnswer)
    {
        $studentAnswer = strtolower(trim($studentAnswer));
        $correctAnswer = strtolower(trim($correctAnswer));

        $correctAnswer = str_replace('option_', '', $correctAnswer);
        $studentAnswer = str_replace('option_', '', $studentAnswer);

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

        return $studentAnswer === $correctAnswer ? 1 : 0;
    }

    private function checkMultipleAnswer($studentAnswer, $correctAnswer)
    {
        $studentAnswers = array_map('trim', explode(',', strtolower($studentAnswer)));
        $correctAnswers = array_map('trim', explode(',', strtolower($correctAnswer)));

        $studentAnswers = array_map(function ($ans) {
            return str_replace('option_', '', $ans);
        }, $studentAnswers);

        $correctAnswers = array_map(function ($ans) {
            return str_replace('option_', '', $ans);
        }, $correctAnswers);

        sort($studentAnswers);
        sort($correctAnswers);

        return $studentAnswers === $correctAnswers ? 1 : 0;
    }

    private function checkTextAnswer($studentAnswer, $correctAnswer)
    {
        $studentAnswer = trim($studentAnswer);
        $correctAnswer = trim($correctAnswer);

        if (empty($correctAnswer)) {
            return !empty($studentAnswer) ? 1 : 0;
        }

        if (strtolower($studentAnswer) === strtolower($correctAnswer)) {
            return 1;
        }

        return 0;
    }

    // ✅ Lihat hasil quiz
    public function result(Request $request, $id)
    {
        $student = $request->user();

        $result = ExercisePoint::where('exercise_id', $id)
            ->where('student_id', $student->id)
            ->with('exercise.exerciseType')
            ->latest()
            ->first();

        if (!$result) {
            return response()->json([
                'success' => false,
                'message' => 'Belum ada hasil untuk latihan ini'
            ], 404);
        }

        $exerciseTypeName = $result->exercise->exerciseType->name ?? '';
        $isPendingReview = $this->isManualGradingType($exerciseTypeName) && $result->exercise_point === null;

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $result->id,
                'exercise_id' => $result->exercise_id,
                'student_id' => $result->student_id,
                'exercise_point' => $result->exercise_point,
                'answer' => $result->answer,
                'created_at' => $result->created_at,
                'updated_at' => $result->updated_at,
                'is_pending_review' => $isPendingReview,
                'exercise_type_name' => $exerciseTypeName,
            ]
        ]);
    }

    public function logActivity(Request $request)
    {
        $student = $request->user();

        $validated = $request->validate([
            'exercise_id' => 'required|exists:exercises,id',
            'event_type' => 'required|string|max:100',
            'duration_seconds' => 'nullable|integer',
            'suspicious_flag' => 'nullable|boolean',
            'timestamp' => 'required|date',
            'device_info' => 'nullable|string|max:255',
        ]);

        try {
            // Cegah double log SUBMIT/AUTO_SUBMIT dalam window 10 detik
            $isSubmitEvent = in_array($validated['event_type'], ['SUBMIT', 'AUTO_SUBMIT']);
            if ($isSubmitEvent) {
                $recentSubmit = DB::connection('mysql_log')
                    ->table('quiz_activity_logs')
                    ->where('student_id', $student->id)
                    ->where('exercise_id', $validated['exercise_id'])
                    ->whereIn('event_type', ['SUBMIT', 'AUTO_SUBMIT'])
                    ->where('created_at', '>=', now()->subSeconds(10))
                    ->exists();

                if ($recentSubmit) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Duplicate submit log ignored'
                    ]);
                }
            }

            DB::connection('mysql_log')->table('quiz_activity_logs')->insert([
                'student_id'      => $student->id,
                'exercise_id'     => $validated['exercise_id'],
                'event_type'      => $validated['event_type'],
                'duration_seconds'=> $validated['duration_seconds'] ?? null,
                'suspicious_flag' => $validated['suspicious_flag'] ?? false,
                'device_info'     => $validated['device_info'] ?? null,
                'ip_address'      => $request->ip(),
                'created_at'      => now(),
            ]);

            // Hapus log lama — simpan hanya 30 hari terakhir per student
            DB::connection('mysql_log')->table('quiz_activity_logs')
                ->where('student_id', $student->id)
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Activity logged'
            ]);
        } catch (\Exception $e) {

            Log::error('Quiz log failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to log activity'
            ], 500);
        }
    }
}
