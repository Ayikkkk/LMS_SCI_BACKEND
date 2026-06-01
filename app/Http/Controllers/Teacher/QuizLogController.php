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

        $exerciseList = Exercise::whereIn('id', $exercises)
            ->with('exerciseType')
            ->get()
            ->map(function ($ex) {
                $logs = DB::connection('mysql_log')
                    ->table('quiz_activity_logs')
                    ->where('exercise_id', $ex->id);

                $suspicious = (clone $logs)->where('suspicious_flag', 1)->count();
                $students   = (clone $logs)->distinct('student_id')->count('student_id');
                $latest     = (clone $logs)->max('created_at');

                return [
                    'id'           => $ex->id,
                    'title'        => $ex->title ?? 'Quiz #' . $ex->id,
                    'type'         => $ex->exerciseType->name ?? '-',
                    'students'     => $students,
                    'suspicious'   => $suspicious,
                    'latest'       => $latest,
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

        // Susun data per siswa
        $data = $students->map(function ($student) use ($exerciseId) {
            $logs = DB::connection('mysql_log')
                ->table('quiz_activity_logs')
                ->where('exercise_id', $exerciseId)
                ->where('student_id', $student->id)
                ->orderBy('created_at')
                ->get();

            $startLog  = $logs->firstWhere('event_type', 'START');
            $submitLog = $logs->first(fn($l) => in_array($l->event_type, ['SUBMIT', 'AUTO_SUBMIT']));

            // Prioritas: ambil duration_seconds dari event SUBMIT/AUTO_SUBMIT
            // Fallback: hitung dari selisih timestamp START → SUBMIT
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

            // Ambil device_info dari log START atau log pertama yang punya nilai
            $deviceInfo = $logs->first(fn($l) => !empty($l->device_info))?->device_info;

            return [
                'student'         => $student,
                'logs'            => $logs,
                'duration'        => $duration,
                'bg_count'        => $bgCount,
                'suspicious'      => $suspiciousCount,
                'back_blocked'    => $backBlocked,
                'is_auto_submit'  => $isAutoSubmit,
                'device_info'     => $deviceInfo,
                'risk_level'      => $this->riskLevel($suspiciousCount, $bgCount, $backBlocked),
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

        if ($score >= 8) return 'high';
        if ($score >= 3) return 'medium';
        return 'low';
    }
}
