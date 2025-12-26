<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\OnlineMeeting;
use Carbon\Carbon;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $student = $request->user();

        $meetings = OnlineMeeting::where('classroom_id', $student->classroom_id)
            ->whereIn('status', ['upcoming', 'live'])
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $meetings
        ]);
    }

    public function show($id)
    {
        $meeting = OnlineMeeting::find($id);

        if (!$meeting) {
            return response()->json([
                'success' => false,
                'message' => 'Meeting tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $meeting
        ]);
    }

    public function join(Request $request, $id)
    {
        $student = $request->user();
        $meeting = OnlineMeeting::findOrFail($id);

        if ($student->classroom_id !== $meeting->classroom_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kelas meeting ini'
            ], 403);
        }

        if ($meeting->status === 'upcoming') {
            $meeting->update([
                'status' => 'live',
                'start_time' => now()
            ]);
        }

        $meeting->participants()->updateOrCreate(
            [
                'online_meeting_id' => $meeting->id,
                'user_id' => $student->id,
                'role' => 'student'
            ],
            [
                'joined_at' => now(),
                'left_at' => null
            ]
        );

        return response()->json([
            'success' => true,
            'meeting_code' => $meeting->meeting_code,
            'jitsi_url' => config('services.jitsi.domain') . '/' . $meeting->meeting_code
        ]);
    }

    public function leave(Request $request, $id)
    {
        $student = $request->user();
        $meeting = OnlineMeeting::findOrFail($id);

        Log::info("Student Leaving Meeting", [
            'student_id' => $student->id ?? 'NULL',
            'meeting_id' => $id
        ]);

        $participant = $meeting->participants()
            ->where('user_id', $student->id)
            ->where('role', 'student')
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Data participant tidak ditemukan'
            ], 404);
        }

        // Durasi minimal agar dianggap benar keluar
        $minDuration = 10; // 10 detik

        if ($participant->left_at === null) {
            if ($participant->joined_at->diffInSeconds(now()) >= $minDuration) {
                $participant->update([
                    'left_at' => now()
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Anda telah keluar dari meeting'
        ]);
    }
}
