<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Classroom;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    /**
     * Daftar kelas milik siswa yang login.
     * Siswa hanya bisa melihat kelas yang memang dia ikuti.
     */
    public function index(Request $request)
    {
        $student = $request->user();

        // Hanya classroom yang diikuti siswa — bukan semua classroom
        $classrooms = Classroom::where('id', $student->classroom_id)
            ->get(['id', 'name', 'grade', 'code']);

        return response()->json([
            'success'    => true,
            'classrooms' => $classrooms
        ]);
    }

    /**
     * Detail kelas — IDOR fix: wajib cocok dengan classroom_id siswa.
     * Siswa tidak bisa mengakses data kelas lain dengan menebak ID.
     *
     * Field sensitif tidak di-expose:
     * - students: hanya id, name, absen_number (bukan email, phone, password, photo)
     * - teacher: hanya id, name (bukan email, token)
     */
    public function show(Request $request, $id)
    {
        $student = $request->user();

        // Authorization: id harus sama dengan classroom_id siswa
        if ((int) $id !== (int) $student->classroom_id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke kelas ini.'
            ], 403);
        }

        $classroom = Classroom::where('id', $id)
            ->with([
                // Batasi kolom siswa — hanya yang dibutuhkan untuk tampil di UI
                // Jangan expose: email, phone, password, photo, token
                'students:id,classroom_id,name,absen_number,nis',
                // teacher() tidak di-load: kolom user_id tidak ada di tabel classrooms,
                // relasi ini selalu null dan menyebabkan query yang sia-sia.
                // Info guru tersedia via Student::guru() jika dibutuhkan.
            ])
            ->select(['id', 'name', 'grade', 'code', 'serial_id'])
            ->first();

        if (!$classroom) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak ditemukan.'
            ], 404);
        }

        return response()->json([
            'success'   => true,
            'classroom' => $classroom
        ]);
    }
}
