<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 💡 PERBAIKAN: Gunakan Eager Loading (.with('classroom')) untuk menyertakan data kelas.
        $student = Student::with('classroom')
            ->where('username', $request->username)
            ->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            // $student sekarang memiliki relasi 'classroom' yang lengkap
            'student' => $student
        ]);
    }

    public function profile(Request $request)
    {
        // PENTING: Saat mengambil profil, Anda juga mungkin perlu Eager Load relasi:
        $student = $request->user('student')->load('classroom');
        return response()->json($student);
    }

    public function logout(Request $request)
    {
        $student = $request->user('student');
        if ($student) {
            $student->tokens()->delete();
        }

        return response()->json(['message' => 'Logout berhasil']);
    }
}