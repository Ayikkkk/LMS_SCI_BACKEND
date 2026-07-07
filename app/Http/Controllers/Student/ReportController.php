<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // Ambil semua laporan milik siswa yang sedang login
    public function index(Request $request)
    {
        $student = $request->user();

        $reports = Report::where('student_id', $student->id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }

    // Detail laporan — IDOR fix: wajib cek student_id agar siswa tidak bisa
    // akses laporan milik siswa lain dengan menebak/increment id
    public function show(Request $request, $id)
    {
        $student = $request->user();

        $report = Report::where('id', $id)
            ->where('student_id', $student->id) // ← IDOR protection
            ->first();

        if (!$report) {
            return response()->json([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $report
        ]);
    }

    // Kirim laporan harian
    public function store(Request $request)
    {
        $student = $request->user();

        try {
            $request->validate([
                'report' => 'required|string',
                'img'    => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
            ]);

            // Decode report JSON dari Flutter
            $decodedReport = json_decode($request->report, true);

            if ($decodedReport === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format report tidak valid'
                ], 422);
            }

            // Upload gambar jika ada
            $imgPath = null;
            if ($request->hasFile('img')) {
                $file = $request->file('img');
                $ext  = strtolower($file->getClientOriginalExtension());

                // Gunakan Str::random(40) untuk nama unik — aman dari collision
                // (pola sama dengan AuthController::updateProfile)
                $safeName = Str::random(40) . '.' . $ext;

                // Simpan ke storage/app/public/reports/
                $file->storeAs('reports', $safeName, 'public');

                // Simpan path lengkap ke DB agar bisa direkonstruksi jadi URL
                $imgPath = 'reports/' . $safeName;
            }

            // Simpan ke database
            $report = Report::create([
                'serial_id'  => $student->serial_id,
                'student_id' => $student->id,
                'report'     => json_encode($decodedReport),
                'img'        => $imgPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Laporan berhasil dikirim',
                'data'    => $report
            ]);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('ReportController@store: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan laporan, coba lagi'
            ], 500);
        }
    }

    // Cek apakah siswa sudah mengisi laporan hari ini
    public function checkToday(Request $request)
    {
        $student = $request->user();
        $today = now()->toDateString();

        $report = Report::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->first();

        return response()->json([
            'success' => true,
            'filled'  => $report !== null
        ]);
    }
}
