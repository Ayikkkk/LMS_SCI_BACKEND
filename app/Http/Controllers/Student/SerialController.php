<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;

class SerialController extends Controller
{
    // Ambil semua serial milik siswa login
    public function index(Request $request)
    {
        $student = $request->user();

        $serials = Serial::with('product')
            ->where('id', $student->serial_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $serials
        ]);
    }

    // Lihat detail serial tertentu
    public function show($id)
    {
        $serial = Serial::with(['product', 'students'])->find($id);

        if (!$serial) {
            return response()->json([
                'success' => false,
                'message' => 'Serial tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $serial
        ]);
    }

    // Aktivasi serial baru (opsional)
    public function activate(Request $request)
    {
        $request->validate([
            'serial' => 'required|string',
        ]);

        $serial = Serial::where('serial', $request->serial)->first();

        if (!$serial) {
            return response()->json(['message' => 'Serial tidak valid'], 404);
        }

        $student = $request->user();
        $student->serial_id = $serial->id;
        $student->save();

        return response()->json([
            'success' => true,
            'message' => 'Serial berhasil diaktivasi',
            'serial' => $serial
        ]);
    }
}
