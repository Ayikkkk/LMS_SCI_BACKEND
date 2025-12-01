<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $student = Student::with('classroom')
            ->where('username', $request->username)
            ->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            return response()->json(['message' => 'Username atau password salah'], 401);
        }

        // Buat token baru
        $token = $student->createToken('student_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'student' => $student
        ]);
    }

    public function profile(Request $request)
    {
        $student = $request->user(); // <-- FIX
        return response()->json(
            $student->load([
                'classroom',
                'guru:id,name,email,phone'
            ])
        );
    }


    public function logout(Request $request)
    {
        // Hapus token yang sedang aktif saja
        $request->user()->currentAccessToken()->delete(); // <-- FIX

        return response()->json(['message' => 'Logout berhasil']);
    }
}
