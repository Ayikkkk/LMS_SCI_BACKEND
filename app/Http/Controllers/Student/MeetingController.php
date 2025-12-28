<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineMeeting;
use App\Models\OnlineMeetingParticipant;

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

    public function join(Request $request, $id)
    {
        $student = $request->user();
        $meeting = OnlineMeeting::findOrFail($id);

        // Validasi kelas
        if ($student->classroom_id !== $meeting->classroom_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak terdaftar dalam kelas meeting ini'
            ], 403);
        }

        // Jika meeting belum dimulai, JANGAN biarkan siswa memulai
        if ($meeting->status === 'upcoming') {
            return response()->json([
                'success' => false,
                'message' => 'Meeting belum dimulai oleh guru'
            ], 403);
        }

        // Insert / update participant siswa
        OnlineMeetingParticipant::updateOrCreate(
            [
                'online_meeting_id' => $meeting->id,
                'user_id' => $student->id,
            ],
            [
                'role' => 'student',
                'joined_at' => now(),
                'left_at' => null,
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

        OnlineMeetingParticipant::where('online_meeting_id', $id)
            ->where('user_id', $student->id)
            ->update([
                'left_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Anda telah keluar dari meeting'
        ]);
    }
}
