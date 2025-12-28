<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostChildComment;
use App\Models\PostComment;
use Illuminate\Support\Facades\Auth;

class PostChildCommentController extends Controller
{
    public function store(Request $request, PostComment $comment)
    {
        $request->validate(['message' => 'required']);

        // Ambil student berdasarkan token (sama seperti komentar utama)
        $student = \App\Models\Student::where('username', Auth::user()->username)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya siswa yang dapat membalas komentar.'
            ], 403);
        }

        $reply = PostChildComment::create([
            'post_comment_id' => $comment->id,
            'student_id' => $student->id,
            'message' => $request->message,
            'is_user' => 0
        ]);

        // 🔥 load relasi baru agar UI bisa langsung akses student & user
        $reply->load(['student', 'user']);

        return response()->json([
            'success' => true,
            'data' => $reply
        ], 201);
    }

    public function destroy(PostChildComment $reply)
    {
        // Ambil student login dari token
        $student = \App\Models\Student::where('username', Auth::user()->username)->first();

        if (!$student || $reply->student_id !== $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $reply->delete();

        return response()->json([
            'success' => true,
            'message' => 'Balasan dihapus'
        ]);
    }
}
