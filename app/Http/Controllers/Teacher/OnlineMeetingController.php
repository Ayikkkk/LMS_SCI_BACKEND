<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\OnlineMeeting;
use App\Models\Classroom;
use App\Models\Serial;
use App\Models\User;

class OnlineMeetingController extends Controller
{
    public function index()
    {
        // TES: guru ID = 1 (karena belum pakai auth)
        $teacher = User::findOrFail(1);

        $meetings = OnlineMeeting::with('classroom')
            ->where('user_id', $teacher->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return view('teacher.online_meetings.index', compact('meetings'));
    }

    public function create()
    {
        // TES: guru ID = 1
        $teacher = User::findOrFail(1);

        // Ambil serial milik guru
        $serial = Serial::where('user_id', $teacher->id)->firstOrFail();

        // Ambil classroom dari serial
        $classrooms = Classroom::where('serial_id', $serial->id)
            ->orderBy('name')
            ->get();

        return view('teacher.online_meetings.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom_id' => 'required|integer',
            'title'        => 'required|string|max:150',
            'start_time'   => 'required|date',
        ]);

        // TES: guru ID = 1
        $teacher = User::findOrFail(1);

        // Ambil serial guru
        $serial = Serial::where('user_id', $teacher->id)->firstOrFail();

        // Validasi classroom milik serial guru
        $classroom = Classroom::where('id', $request->classroom_id)
            ->where('serial_id', $serial->id)
            ->firstOrFail();

        OnlineMeeting::create([
            'classroom_id' => $classroom->id,
            'user_id'      => $teacher->id,
            'title'        => $request->title,
            'description'  => $request->description,
            'meeting_code' => 'meet-' . Str::random(10),
            'start_time'   => $request->start_time,
            'status'       => 'upcoming',
        ]);

        return redirect('/teacher/online-meetings')
            ->with('success', 'Online meeting berhasil dibuat');
    }

    public function start($id)
    {
        $teacher = User::findOrFail(1);

        $meeting = OnlineMeeting::where('id', $id)
            ->where('user_id', $teacher->id)
            ->firstOrFail();

        $meeting->update([
            'status' => 'live'
        ]);

        $jitsiUrl = config('services.jitsi.domain') . '/' . $meeting->meeting_code;

        return redirect()->away($jitsiUrl);
    }

    public function end($id)
    {
        $teacher = User::findOrFail(1);

        $meeting = OnlineMeeting::where('id', $id)
            ->where('user_id', $teacher->id)
            ->firstOrFail();

        $meeting->update([
            'status'   => 'ended',
            'end_time' => Carbon::now(),
        ]);

        return back()->with('success', 'Meeting telah diakhiri');
    }
}
