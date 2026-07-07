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
            ->get()
            ->map(function ($meeting) {
                return [
                    'id'           => $meeting->id,
                    'classroom_id' => $meeting->classroom_id,
                    'title'        => $meeting->title,
                    'description'  => $meeting->description,
                    'meeting_code' => $meeting->meeting_code,
                    'start_time'   => $meeting->start_time
                        ? \Carbon\Carbon::parse($meeting->getRawOriginal('start_time'), 'Asia/Jakarta')
                            ->toIso8601String()
                        : null,
                    'end_time'     => $meeting->end_time
                        ? \Carbon\Carbon::parse($meeting->getRawOriginal('end_time'), 'Asia/Jakarta')
                            ->toIso8601String()
                        : null,
                    'status'       => $meeting->status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $meetings
        ]);
    }

    public function join(Request $request, $id)
    {
        $student = $request->user();

        // Wrap dalam transaksi + lockForUpdate untuk menghindari TOCTOU:
        // - Dua request join bersamaan dari siswa yang sama tidak akan membuat duplikat
        // - Status meeting tidak bisa berubah di antara cek dan insert
        $result = \Illuminate\Support\Facades\DB::transaction(function () use ($student, $id) {

            // Lock baris meeting untuk durasi transaksi ini
            // Mencegah race condition saat status berubah bersamaan
            $meeting = OnlineMeeting::lockForUpdate()->find($id);

            if (!$meeting) {
                return ['error' => 'not_found', 'status' => 404];
            }

            // Validasi kelas — siswa hanya bisa join kelas sendiri
            if ($student->classroom_id !== $meeting->classroom_id) {
                return ['error' => 'forbidden_classroom', 'status' => 403];
            }

            // Cek status meeting di dalam transaksi (atomik)
            if ($meeting->status === 'upcoming') {
                return ['error' => 'upcoming', 'status' => 403];
            }

            if ($meeting->status === 'ended') {
                return ['error' => 'ended', 'status' => 403];
            }

            // updateOrCreate aman dari duplikat karena:
            // 1. DB punya UNIQUE(online_meeting_id, user_id)
            // 2. Kita di dalam lockForUpdate transaction
            OnlineMeetingParticipant::updateOrCreate(
                [
                    'online_meeting_id' => $meeting->id,
                    'user_id'           => $student->id,
                ],
                [
                    'role'      => 'student',
                    'joined_at' => now(),
                    'left_at'   => null,
                ]
            );

            return [
                'meeting_code' => $meeting->meeting_code,
                'jitsi_url'    => config('services.jitsi.domain') . '/' . $meeting->meeting_code,
            ];
        });

        // Handle error results dari transaksi
        if (isset($result['error'])) {
            $messages = [
                'not_found'          => 'Meeting tidak ditemukan',
                'forbidden_classroom'=> 'Anda tidak terdaftar dalam kelas meeting ini',
                'upcoming'           => 'Meeting belum dimulai oleh guru',
                'ended'              => 'Meeting sudah berakhir',
            ];
            return response()->json([
                'success' => false,
                'message' => $messages[$result['error']] ?? 'Tidak dapat bergabung',
            ], $result['status']);
        }

        return response()->json([
            'success'      => true,
            'meeting_code' => $result['meeting_code'],
            'jitsi_url'    => $result['jitsi_url'],
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
