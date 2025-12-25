<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineMeeting;
use Carbon\Carbon;

class MeetingController extends Controller
{
    /**
     * List meeting berdasarkan classroom siswa
     */
    public function index(Request $request)
    {
        $student = $request->user();

        // asumsi student punya classroom_id
        $meetings = OnlineMeeting::where('classroom_id', $student->classroom_id)
            ->whereIn('status', ['upcoming', 'live'])
            ->orderBy('start_time', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $meetings
        ]);
    }

    /**
     * Detail meeting
     */
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

    /**
     * Join meeting (untuk absensi)
     */
    public function join(Request $request, $id)
    {
        $student = $request->user();
        $meeting = OnlineMeeting::findOrFail($id);

        $meeting->participants()->create([
            'user_id'   => $student->id,
            'role'      => 'student',
            'joined_at' => Carbon::now(),
        ]);

        return response()->json([
            'success' => true,
            'meeting_code' => $meeting->meeting_code,
            'jitsi_url' => config('services.jitsi.domain') . '/' . $meeting->meeting_code
        ]);
    }
}
