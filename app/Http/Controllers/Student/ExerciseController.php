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

        // Tampilkan semua exercise dengan serial_id yang sama
        $lessons = Lesson::whereHas('exercises', function ($q) use ($student) {
            $q->where('exercises.serial_id', $student->serial_id);
        })->with(['exercises' => function ($q) use ($student) {
            $q->where('exercises.serial_id', $student->serial_id)
              ->with('exerciseType');
        }])->orderBy('name')->get();

        // Ambil exercise yang sudah di-share ke serial+classroom siswa
        $allExerciseIds = $lessons->flatMap(fn($l) => $l->exercises->pluck('id'));

        $sharedIds = \App\Models\ShareExercise::whereIn('exercise_id', $allExerciseIds)
            ->where('serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->pluck('exercise_id')
            ->flip(); // flip agar bisa isset()

        // Ambil exercise yang sudah dikerjakan siswa
        $doneIds = ExercisePoint::where('student_id', $student->id)
            ->whereIn('exercise_id', $allExerciseIds)
            ->pluck('exercise_id')
            ->flip();

        $data = $lessons->map(function ($lesson) use ($doneIds, $sharedIds) {
            $types = $lesson->exercises->groupBy(function ($ex) {
                return $ex->exerciseType ? $ex->exerciseType->id : null;
            })->map(function ($group, $key) use ($doneIds, $sharedIds) {
                if ($key === null) return null;
                $type = $group->first()->exerciseType;
                $total   = $group->count();
                $done    = $group->filter(fn($ex) => isset($doneIds[$ex->id]))->count();
                // Terkunci jika TIDAK ADA satupun exercise di tipe ini yang sudah di-share
                $unlockedCount = $group->filter(fn($ex) => isset($sharedIds[$ex->id]))->count();
                return [
                    'id'             => $type->id,
                    'kode'           => $type->kode,
                    'name'           => $type->name,
                    'count'          => $total,
                    'done_count'     => $done,
                    'pending_count'  => $total - $done,
                    'unlocked_count' => $unlockedCount,
                    'is_locked'      => $unlockedCount === 0,
                ];
            })->filter()->values();

            return [
                'id'       => $lesson->id,
                'mapel_id' => $lesson->mapel_id,
                'name'     => $lesson->name,
                'grade'    => $lesson->grade,
                'semester' => $lesson->semester,
                'category' => $lesson->category,
                'types'    => $types,
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // Daftar exercise untuk lesson tertentu
    public function exercisesByLesson(Request $request, $lessonId)
    {
        $student = $request->user();
        $typeId = $request->query('type_id');

        // Tampilkan semua exercise milik serial siswa
        $query = Exercise::with('exerciseType')
            ->where('lesson_id', $lessonId)
            ->where('exercises.serial_id', $student->serial_id);

        if ($typeId) {
            $query->where('exercise_type_id', $typeId);
        }

        $exercises = $query->orderBy('id', 'desc')->get();

        $exerciseIds = $exercises->pluck('id');

        // Cek mana yang sudah di-share
        $sharedIds = \App\Models\ShareExercise::whereIn('exercise_id', $exerciseIds)
            ->where('serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })
            ->pluck('exercise_id')
            ->flip();

        // Ambil semua hasil kuis siswa untuk exercise di lesson ini sekaligus
        $points = ExercisePoint::where('student_id', $student->id)
            ->whereIn('exercise_id', $exerciseIds)
            ->get()
            ->keyBy('exercise_id');

        $data = $exercises->map(function ($ex) use ($points, $sharedIds) {
            $point = $points->get($ex->id);
            return [
                'id'               => $ex->id,
                'lesson_id'        => $ex->lesson_id,
                'serial_id'        => $ex->serial_id,
                'exercise_type_id' => $ex->exercise_type_id,
                'title'            => $ex->title,
                'is_admin'         => $ex->is_admin,
                'created_at'       => $ex->created_at,
                'updated_at'       => $ex->updated_at,
                'deleted_at'       => $ex->deleted_at,
                'exercise_type'    => $ex->exerciseType,
                // Status pengerjaan
                'is_done'          => $point !== null,
                'score'            => $point ? $point->exercise_point : null,
                'is_pending_review'=> $point && $point->exercise_point === null,
                // Terkunci jika belum di-share guru
                'is_locked'        => !isset($sharedIds[$ex->id]),
            ];
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    //  Detail quiz & soal
    public function show(Request $request, $id)
    {
        $student = $request->user();
        $exercise = Exercise::with(['items', 'exerciseType'])->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Latihan tidak ditemukan'
            ], 404);
        }

        // Validasi akses: serial_id langsung HARUS cocok
        if ($exercise->serial_id !== null && $exercise->serial_id !== $student->serial_id) {
            return response()->json([
                'success' => false,
                'message' => 'Akses latihan tidak diizinkan'
            ], 403);
        }

        // Cek apakah sudah di-share (tidak terkunci)
        $isShared = \App\Models\ShareExercise::where('exercise_id', $id)
            ->where('serial_id', $student->serial_id)
            ->where(function ($q) use ($student) {
                $q->whereNull('classroom_id')
                  ->orWhere('classroom_id', $student->classroom_id);
            })->exists();

        if (!$isShared) {
            return response()->json([
                'success' => false,
                'message' => 'Kuis ini belum dibuka oleh guru',
                'is_locked' => true,
            ], 403);
        }

        /** @var Exercise $exercise */
        // Format items dengan tipe soal yang benar, lalu acak urutan SOAL
        // (urutan OPSI tidak diacak agar jawaban a/b/c/d tetap konsisten dengan DB)
        $formattedItems = $exercise->items->map(function ($item) {
            // Kirim HTML asli untuk mendukung gambar, kemudian teks bersih sebagai fallback
            $questionHtml = $item->question ?? '';
            $questionText = strip_tags($questionHtml);

            $options     = $this->parseOptions($item->selection, $item->exercise_model_id);
            $optionsHtml = $this->parseOptionsHtml($item->selection, $item->exercise_model_id);
            // $correctAnswer tidak dihitung di sini — digunakan hanya saat submit (checkSingleAnswer dll)

            return [
                'id'               => $item->id,
                'question'         => $questionText,
                'question_html'    => $questionHtml,
                'options'          => $options,
                'options_html'     => $optionsHtml,
                // correct_answer TIDAK dikirim ke client — scoring dilakukan di backend
                // Field ini sebelumnya bocor jawaban ke siswa yang inspect network traffic
                'type'             => $this->mapModelIdToType($item->exercise_model_id),
                'is_multiple'      => $item->exercise_model_id == 2,
                'allow_multiple'   => $item->exercise_model_id == 2,
                'multiple_correct' => $item->exercise_model_id == 2,
                'exercise_model_id'=> $item->exercise_model_id,
                'exercise_choice'  => $item->exercise_choice,
            ];
        })
            ->shuffle() // acak urutan SOAL saja
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

    //  Parse options dari kolom 'selection' — kembalikan teks bersih (backward compat)
    private function parseOptions($selection, $modelId)
    {
        $htmlOptions = $this->parseOptionsHtml($selection, $modelId);
        return array_map('strip_tags', $htmlOptions);
    }

    //  Parse options HTML asli (dengan gambar jika ada)
    private function parseOptionsHtml($selection, $modelId)
    {
        if (in_array($modelId, [4, 5, 7])) {
            return [];
        }

        if (empty($selection)) {
            return [];
        }

        $trimmed = trim($selection);

        // Coba json_decode langsung (handles: ["<p>Es</p>",...] dan ["<p>Es<\/p>",...])
        if (str_starts_with($trimmed, '[') || str_starts_with($trimmed, '{')) {
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded) && !empty($decoded)) {
                return array_values($decoded);
            }

            // Fallback 1: stripcslashes untuk double-escape [\"] → ["
            $unescaped = stripcslashes($trimmed);
            $decoded2  = json_decode($unescaped, true);
            if (is_array($decoded2) && !empty($decoded2)) {
                return array_values($decoded2);
            }

            // Fallback 2: ganti \" → " manual lalu decode
            $replaced = str_replace('\\"', '"', $trimmed);
            $decoded3  = json_decode($replaced, true);
            if (is_array($decoded3) && !empty($decoded3)) {
                return array_values($decoded3);
            }
        }

        // Fallback 3: mungkin selection tersimpan sebagai JSON string (outer-encoded)
        // Contoh: '"[\"<p>Es<\\/p>\",...]"' → decode outer dulu
        if (str_starts_with($trimmed, '"') && str_ends_with($trimmed, '"')) {
            $outerDecoded = json_decode($trimmed, true);
            if (is_string($outerDecoded)) {
                $inner = json_decode($outerDecoded, true);
                if (is_array($inner) && !empty($inner)) {
                    return array_values($inner);
                }
                $inner2 = json_decode(stripcslashes($outerDecoded), true);
                if (is_array($inner2) && !empty($inner2)) {
                    return array_values($inner2);
                }
            }
        }

        // Fallback: split per newline atau koma
        if (strpos($trimmed, "\n") !== false) {
            $options = array_map('trim', explode("\n", $trimmed));
        } else {
            $options = array_map('trim', explode(',', $trimmed));
        }

        return array_values(array_filter($options, fn($opt) => !empty(trim($opt))));
    }

    /**
     * Normalisasi jawaban dari DB ke huruf kecil tunggal atau array huruf kecil.
     * Menangani berbagai format:
     *   "B"          → "b"
     *   '["B"]'      → "b"
     *   '"[\"B\"]"'  → "b"   (outer-quoted JSON string, triple-encoded)
     *   '["A","C"]'  → "a,c"
     */
    private function normalizeAnswer(?string $raw): string
    {
        if (empty($raw)) return '';

        $trimmed = trim($raw);

        // Langkah 1: jika dibungkus outer double-quote (triple-encoded),
        // decode outer string dulu → hasilnya string seperti '["B"]'
        if (str_starts_with($trimmed, '"') && str_ends_with($trimmed, '"')) {
            $outer = json_decode($trimmed, true);
            if (is_string($outer)) {
                $trimmed = trim($outer);
            }
        }

        // Langkah 2: jika JSON array
        if (str_starts_with($trimmed, '[')) {
            $decoded = json_decode($trimmed, true);

            // Jika gagal, coba stripcslashes dulu
            if (!is_array($decoded)) {
                $decoded = json_decode(stripcslashes($trimmed), true);
            }

            if (is_array($decoded)) {
                $letters = array_map(function ($v) {
                    return strtolower(trim(strip_tags($v)));
                }, $decoded);
                sort($letters);
                return implode(',', $letters);
            }
        }

        // Langkah 3: plain string "B" atau "b"
        return strtolower(strip_tags(trim($trimmed)));
    }

    //  Map exercise_model_id ke tipe Flutter
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

    //  Cek apakah tipe exercise perlu penilaian manual
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

    //  Submit quiz - CORRECTED (bagian awal yang hilang sudah ditambahkan)
    public function submit(Request $request, $id)
    {
        $student = $request->user();

        // ============================================================
        // CEK AWAL — sudah pernah submit sebelumnya (quick check, bukan atomik)
        // Race condition masih mungkin di sini, tapi ditangani di DB constraint di bawah
        // ============================================================
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

        // HAPUS: jangan terima local_score dari client — backend hitung sendiri
        // $localScore = $request->input('local_score'); ← dihapus karena security risk

        //  AMBIL INFO EXERCISE TYPE
        $exercise = Exercise::with('exerciseType')->find($id);

        if (!$exercise) {
            return response()->json([
                'success' => false,
                'message' => 'Exercise tidak ditemukan'
            ], 404);
        }

        $exerciseTypeName = $exercise->exerciseType->name ?? '';

        //  CEK APAKAH PERLU MANUAL GRADING
        $isPendingReview = $this->isManualGradingType($exerciseTypeName);

        $questions = ExerciseItem::where('exercise_id', $id)
            ->get(['id', 'exercise_model_id', 'answer']); // hanya kolom untuk scoring
        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada soal untuk latihan ini'
            ], 400);
        }

        $score = null;

        //  HANYA HITUNG SKOR JIKA BUKAN MANUAL GRADING
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
            // Score selalu dihitung backend — tidak menerima local_score dari client
        }

        // ============================================================
        // SIMPAN KE DATABASE — dilindungi oleh UNIQUE constraint
        // Jika dua request bersamaan lolos cek di atas, DB akan throw
        // UniqueConstraintViolationException untuk request kedua
        // ============================================================
        try {
            $point = DB::transaction(function () use ($student, $id, $answers, $score) {
                return ExercisePoint::create([
                    'serial_id'      => $student->serial_id,
                    'exercise_id'    => $id,
                    'student_id'     => $student->id,
                    'answer'         => json_encode($answers),
                    'exercise_point' => $score,
                ]);
            });
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            // Race condition tertangkap di DB level — kembalikan response "sudah dikerjakan"
            // Ini terjadi jika dua request submit bersamaan, keduanya lolos cek awal,
            // tapi hanya satu yang berhasil insert karena unique constraint
            $existing = ExercisePoint::where('exercise_id', $id)
                ->where('student_id', $student->id)
                ->first();

            return response()->json([
                'success' => false,
                'message' => 'Quiz sudah pernah dikerjakan',
                'data' => $existing
            ], 403);
        } catch (\Exception $e) {
            Log::error('ExerciseController@submit DB error: ' . $e->getMessage(), [
                'exercise_id' => $id,
                'student_id'  => $student->id,
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan hasil kuis. Coba lagi.',
            ], 500);
        }

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
        // Normalisasi kedua sisi: lowercase, strip HTML, decode JSON array
        $student = strtolower(trim(str_replace('option_', '', $studentAnswer)));
        $correct = $this->normalizeAnswer($correctAnswer);

        return $student === $correct ? 1 : 0;
    }

    private function checkMultipleAnswer($studentAnswer, $correctAnswer)
    {
        // studentAnswer: "a,c" atau "option_a,option_c"
        $studentLetters = array_map(function ($s) {
            return strtolower(trim(str_replace('option_', '', $s)));
        }, explode(',', $studentAnswer));
        sort($studentLetters);

        // correctAnswer: '["A","C"]' atau "a,c"
        $correctNorm = $this->normalizeAnswer($correctAnswer);
        $correctLetters = array_map('trim', explode(',', $correctNorm));
        sort($correctLetters);

        return $studentLetters === $correctLetters ? 1 : 0;
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

    //  Lihat hasil quiz
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
                'student_id'       => $student->id,
                'exercise_id'      => $validated['exercise_id'],
                'event_type'       => $validated['event_type'],
                'duration_seconds' => $validated['duration_seconds'] ?? null,
                'suspicious_flag'  => $validated['suspicious_flag'] ?? false,
                'device_info'      => $validated['device_info'] ?? null,
                'ip_address'       => $request->ip(),
                'created_at'       => now(),
            ]);

            // DELETE log lama TIDAK dijalankan di sini — dipindah ke scheduled job
            // agar tidak membebani setiap request saat banyak siswa mengerjakan kuis bersamaan

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
