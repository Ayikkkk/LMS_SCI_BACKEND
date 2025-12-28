<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\OnlineMeeting;
use App\Models\OnlineMeetingParticipant;
use App\Models\Classroom;
use App\Models\Serial;
use App\Models\User;

class OnlineMeetingController extends Controller
{
    /**
     * Temporary fallback untuk testing
     * ⚠️ PRODUKSI: hapus User::findOrFail(1)
     */
    private function getTeacher()
    {
        return Auth::user() ?? User::findOrFail(1);
    }

    public function index()
    {
        $teacher = $this->getTeacher();

        $meetings = OnlineMeeting::with('classroom')
            ->where('user_id', $teacher->id)
            ->orderBy('start_time', 'desc')
            ->get();

        return view('teacher.online_meetings.index', compact('meetings'));
    }

    public function create()
    {
        $teacher = $this->getTeacher();

        $serials = Serial::where('user_id', $teacher->id)->get();

        $classrooms = Classroom::whereIn('serial_id', $serials->pluck('id'))
            ->orderBy('name')
            ->get();

        return view('teacher.online_meetings.create', compact('classrooms'));
    }

    public function store(Request $request)
    {
        $teacher = $this->getTeacher();

        $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'title'        => 'required|string|max:150',
            'start_time'   => 'required|date',
        ]);

        $classroom = Classroom::findOrFail($request->classroom_id);

        /**
         * 🔥 PENTING
         * Input guru = WIB
         * Simpan ke DB = UTC
         */
        $startTimeUtc = Carbon::parse(
            $request->start_time,
            'Asia/Jakarta'
        )->utc();

        OnlineMeeting::create([
            'serial_id'    => $classroom->serial_id,
            'classroom_id' => $classroom->id,
            'user_id'      => $teacher->id,
            'title'        => $request->title,
            'description'  => $request->description,
            'meeting_code' => 'meet-' . Str::random(10),
            'start_time'   => $startTimeUtc,
            'status'       => 'upcoming',
        ]);

        return redirect('/teacher/online-meetings')
            ->with('success', 'Online meeting berhasil dibuat');
    }

    public function start($id)
    {
        $teacher = $this->getTeacher();

        $meeting = OnlineMeeting::where('id', $id)
            ->where('user_id', $teacher->id)
            ->firstOrFail();

        // Set meeting LIVE (UTC)
        $meeting->update([
            'status'     => 'live',
            'start_time' => now()->utc(),
        ]);

        // Catat guru sebagai participant
        OnlineMeetingParticipant::updateOrCreate(
            [
                'online_meeting_id' => $meeting->id,
                'user_id'           => $teacher->id,
            ],
            [
                'role'      => 'teacher',
                'joined_at' => now()->utc(),
                'left_at'   => null,
            ]
        );

        return redirect()->away(
            config('services.jitsi.domain') . '/' . $meeting->meeting_code
        );
    }

    public function end($id)
    {
        $teacher = $this->getTeacher();

        $meeting = OnlineMeeting::where('id', $id)
            ->where('user_id', $teacher->id)
            ->firstOrFail();

        $meeting->update([
            'status'   => 'ended',
            'end_time' => now()->utc(),
        ]);

        // Tutup semua participant yang belum leave
        OnlineMeetingParticipant::where('online_meeting_id', $meeting->id)
            ->whereNull('left_at')
            ->update([
                'left_at' => now()->utc(),
            ]);

        return back()->with('success', 'Meeting telah diakhiri');
    }
}
