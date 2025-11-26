<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Report;

class ReportController extends Controller
{
    // Ambil semua laporan milik siswa login
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

    // Detail laporan tertentu
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

    // Buat laporan baru
    public function store(Request $request)
    {
        $student = $request->user();

        $request->validate([
            'report' => 'required|string',
            'img' => 'nullable|file|mimes:jpg,jpeg,png|max:2048'
        ]);

        $imgPath = null;
        if ($request->hasFile('img')) {
            $imgPath = $request->file('img')->store('reports', 'public');
        }

        $report = Report::create([
            'serial_id' => $student->serial_id,
            'student_id' => $student->id,
            'report' => $request->report,
            'img' => $imgPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Laporan berhasil dikirim',
            'data' => $report
        ]);
    }
}
