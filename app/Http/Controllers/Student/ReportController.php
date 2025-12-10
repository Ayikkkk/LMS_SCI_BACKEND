<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    // Ambil semua laporan user
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

    // Detail laporan
    public function show($id)
    {
        $report = Report::find($id);

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
                'img' => 'nullable|image|mimes:jpg,jpeg,png|max:4096',
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

                // ambil nama asli file
                $originalName = $file->getClientOriginalName();

                // slug agar aman
                $name = pathinfo($originalName, PATHINFO_FILENAME);
                $ext  = $file->getClientOriginalExtension();

                // final filename => nama-asli-yang-sudah-dirapikan + timestamp
                $safeName = Str::slug($name) . '-' . time() . '.' . strtolower($ext);

                // simpan ke folder public/storage/reports
                $file->storeAs('public/reports', $safeName);

                // Simpan ke database
                $imgPath = $safeName;
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

            // Debug server error
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
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
            'filled' => $report ? true : false
        ]);
    }
}
