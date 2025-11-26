<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\OnlineMeeting;

class MeetingController extends Controller
{
    // Semua meeting milik serial siswa login
    public function index(Request $request)
    {
        $student = $request->user();

        $meetings = OnlineMeeting::where('serial_id', $student->serial_id)
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $meetings
        ]);
    }

    // Detail meeting
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
}
