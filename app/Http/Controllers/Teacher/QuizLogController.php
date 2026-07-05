<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Exercise;
use App\Models\Student;

class QuizLogController extends Controller
{
    /**
     * Daftar semua exercise yang punya log aktivitas
     */
    public function index(Request $request)
    {
        $exercises = DB::connection('mysql_log')
            ->table('quiz_activity_logs')
            ->select('exercise_id')
            ->distinct()
            ->pluck('exercise_id');

        // Ambil semua stats sekaligus dalam 1 query (bukan N query per exercise)
        $stats = DB::connection('mysql_log')
            ->table('quiz_activity_logs')
            ->whereIn('exercise_id', $exercises)
            ->selectRaw('
                exercise_id,
                COUNT(CASE WHEN suspicious_flag = 1 THEN 1 END) as suspicious,
                COUNT(DISTINCT student_id) as students,
                MAX(created_at) as latest
            ')
            ->groupBy('exercise_id')
            ->get()
            ->keyBy('exercise_id');

        $exerciseList = Exercise::whereIn('id', $exercises)
            ->with('exerciseType')
            ->get()
            ->map(function ($ex) use ($stats) {
                $s = $stats->get($ex->id);
                return [
                    'id'         => $ex->id,
                    'title'      => $ex->title ?? "Quiz #{$ex->id}",
                    'type'       => $ex->exerciseType->name ?? '-',
                    'students'   => $s?->students ?? 0,
                    'suspicious' => $s?->suspicious ?? 0,
                    'latest'     => $s?->latest,
                ];
            });

        return view('teacher.quiz_logs.index', compact('exerciseList'));
    }

    /**
     * Detail log per exercise — tampilkan per siswa
     */
    public function show(Request $request, $exerciseId)
    {
        $exercise = Exercise::with('exerciseType')->findOrFail($exerciseId);

        // Ambil semua student yang punya log di exercise ini
        $studentIds = DB::connection('mysql_log')
            ->table('quiz_activity_logs')
            ->where('exercise_id', $exerciseId)
            ->distinct()
            ->pluck('student_id');

        $students = Student::whereIn('id', $studentIds)->get(['id', 'name', 'nis']);

        // Ambil SEMUA logs untuk exercise ini sekaligus — 1 query, bukan N query
        $allLogs = DB::connection('mysql_log')
            ->table('quiz_activity_logs')
            ->where('exercise_id', $exerciseId)
            ->orderBy('created_at')
            ->get()
            ->groupBy('student_id');

        // Susun data per siswa
        $data = $students->map(function ($student) use ($allLogs) {
            $logs = $allLogs->get($student->id, collect());

            $startLog  = $logs->firstWhere('event_type', 'START');
            $submitLog = $logs->first(fn($l) => \in_array($l->event_type, ['SUBMIT', 'AUTO_SUBMIT']));

            $duration = null;
            if ($submitLog && $submitLog->duration_seconds !== null) {
                $duration = $submitLog->duration_seconds;
            } elseif ($startLog && $submitLog) {
                $duration = \Carbon\Carbon::parse($startLog->created_at)
                    ->diffInSeconds(\Carbon\Carbon::parse($submitLog->created_at));
            }

            $bgCount         = $logs->where('event_type', 'APP_BACKGROUND')->count();
            $suspiciousCount = $logs->where('suspicious_flag', 1)->count();
            $backBlocked     = $logs->where('event_type', 'BACK_BUTTON_BLOCKED')->count();
            $isAutoSubmit    = $logs->contains('event_type', 'AUTO_SUBMIT');
            $deviceInfo      = $logs->first(fn($l) => !empty($l->device_info))?->device_info;

            return [
                'student'        => $student,
                'logs'           => $logs,
                'duration'       => $duration,
                'bg_count'       => $bgCount,
                'suspicious'     => $suspiciousCount,
                'back_blocked'   => $backBlocked,
                'is_auto_submit' => $isAutoSubmit,
                'device_info'    => $deviceInfo,
                'risk_level'     => $this->riskLevel($suspiciousCount, $bgCount, $backBlocked),
            ];
        })->sortByDesc(fn($d) => $d['suspicious']);

        return view('teacher.quiz_logs.show', compact('exercise', 'data'));
    }


    /**
     * Hitung level risiko kecurangan
     */
    private function riskLevel(int $suspicious, int $bg, int $backBlocked): string
    {
        $score = ($suspicious * 3) + ($bg * 1) + ($backBlocked * 2);

        if ($score >= 8) return 'Beresiko Tinggi';
        if ($score >= 3) return 'Perlu Perhatian';
        return 'Normal';
    }
}
