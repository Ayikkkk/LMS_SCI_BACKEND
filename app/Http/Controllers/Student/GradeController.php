<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\ExercisePoint;
use App\Models\Lesson;
use App\Models\Post;
use App\Models\Exercise;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class GradeController extends Controller
{

    public function recapPerMapel(Request $request)
    {
        $student = $request->user();
        $classroom = $student->classroom;

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa belum terdaftar di kelas'
            ], 422);
        }

        /**
         * ==========================
         * AMBIL MAPEL DARI TUGAS
         * ==========================
         */
        $mapelFromTasks = Post::where('is_task', 1)
            ->where('serial_id', $student->serial_id)
            ->pluck('mapel_id');

        /**
         * ==========================
         * AMBIL LESSON SESUAI KELAS
         * ==========================
         */
        $lessons = Lesson::where('grade', $classroom->grade)->get();

        $mapelFromLessons = $lessons->pluck('mapel_id');

        /**
         * ==========================
         * GABUNG MAPEL
         * ==========================
         */
        $mapelIds = $mapelFromTasks
            ->merge($mapelFromLessons)
            ->unique()
            ->values();

        $mapels = DB::table('mapels')
            ->whereIn('id', $mapelIds)
            ->orderBy('name')
            ->get();

        /**
         * ==========================
         * DATA DETAIL
         * ==========================
         */
        $taskPosts = Post::where('is_task', 1)
            ->where('serial_id', $student->serial_id)
            ->get(['id', 'mapel_id', 'title']);

        $exercises = Exercise::where('serial_id', $student->serial_id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get(['id', 'lesson_id', 'title']);

        $taskPoints = Task::where('student_id', $student->id)->get();
        $exercisePoints = ExercisePoint::where('student_id', $student->id)->get();

        $rows = [];

        foreach ($mapels as $mapel) {
            $headers = collect();
            $scores = [];

            /**
             * ============
             * TUGAS
             * ============
             */
            foreach ($taskPosts->where('mapel_id', $mapel->id) as $post) {
                $headers->push($post->title);

                $task = $taskPoints->firstWhere('post_id', $post->id);
                $scores[$post->title] =
                    $task && $task->point !== null ? (int) $task->point : '-';
            }

            /**
             * ============
             * QUIZ
             * ============
             */
            foreach ($lessons->where('mapel_id', $mapel->id) as $lesson) {
                foreach ($exercises->where('lesson_id', $lesson->id) as $exercise) {
                    $headers->push($exercise->title);

                    $point = $exercisePoints
                        ->firstWhere('exercise_id', $exercise->id);

                    $scores[$exercise->title] =
                        $point && $point->exercise_point !== null
                        ? (int) $point->exercise_point
                        : '-';
                }
            }

            if ($headers->isNotEmpty()) {
                $rows[] = [
                    'mapel'   => $mapel->name,
                    'headers' => $headers->unique()->values(),
                    'scores'  => $scores
                ];
            }
        }

        return response()->json([
            'success' => true,
            'student' => [
                'nis'   => $student->nis,
                'name'  => $student->name,
                'kelas' => $classroom->name
            ],
            'rows' => $rows
        ]);
    }

    public function downloadRecapPdf(Request $request)
    {
        // Ambil data rekap dalam bentuk array
        $response = $this->recapPerMapel($request)->getData(true);

        if (!isset($response['success']) || $response['success'] !== true) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat PDF rekap nilai'
            ], 422);
        }

        $pdf = app('dompdf.wrapper')
            ->loadView('pdf.rekap-nilai', [
                'student' => $response['student'],
                'rows'    => $response['rows'],
            ])
            ->setPaper('A4', 'landscape');

        return $pdf->download(
            'rekap-nilai-' . $response['student']['nis'] . '.pdf'
        );
    }


    /**
     * 📘 Daftar nilai tugas siswa
     */
    public function taskGrades(Request $request)
    {
        $student = $request->user();

        $tasks = Task::with([
            'post:id,title,course_id'
        ])
            ->where('student_id', $student->id)
            ->whereNotNull('point')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'post_id',
                'description',
                'point',
                'attachment',
                'created_at'
            ]);

        return response()->json([
            'success' => true,
            'meta' => [
                'total' => $tasks->count(),
            ],
            'data' => $tasks
        ]);
    }

    /**
     *  Daftar nilai latihan / quiz siswa
     */
    public function exerciseGrades(Request $request)
    {
        $student = $request->user();

        $exercises = ExercisePoint::with([
            'exercise:id,title,course_id'
        ])
            ->where('student_id', $student->id)
            ->whereNotNull('exercise_point')
            ->orderByDesc('created_at')
            ->get([
                'id',
                'exercise_id',
                'exercise_point',
                'created_at'
            ]);

        return response()->json([
            'success' => true,
            'meta' => [
                'total' => $exercises->count(),
            ],
            'data' => $exercises
        ]);
    }

    /**
     * Rekap nilai dashboard (ringkas & cepat)
     */
    public function summary(Request $request)
    {
        $student = $request->user();

        $taskStats = Task::where('student_id', $student->id)
            ->whereNotNull('point')
            ->selectRaw('
                COUNT(id) as total,
                ROUND(AVG(point), 2) as average
            ')
            ->first();

        $exerciseStats = ExercisePoint::where('student_id', $student->id)
            ->whereNotNull('exercise_point')
            ->selectRaw('
                COUNT(id) as total,
                ROUND(AVG(exercise_point), 2) as average
            ')
            ->first();

        $avgTask = $taskStats->average ?? 0;
        $avgExercise = $exerciseStats->average ?? 0;

        return response()->json([
            'success' => true,
            'data' => [
                'tasks' => [
                    'total' => (int) $taskStats->total,
                    'average' => (float) $avgTask,
                ],
                'exercises' => [
                    'total' => (int) $exerciseStats->total,
                    'average' => (float) $avgExercise,
                ],
                'final_score' => round(($avgTask + $avgExercise) / 2, 2)
            ]
        ]);
    }
}
