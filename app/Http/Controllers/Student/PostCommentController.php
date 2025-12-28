<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostComment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostCommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = PostComment::with(['student', 'user', 'replies.student', 'replies.user'])
            ->where('post_id', $post->id)
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);
    }

    public function store(Request $request, Post $post)
    {
        $request->validate(['message' => 'required']);

        $student = \App\Models\Student::where('username', Auth::user()->username)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 403);
        }

        $comment = PostComment::create([
            'post_id' => $post->id,
            'student_id' => $student->id,
            'message' => $request->message,
            'code' => uniqid('CMT'),
            'is_user' => 0
        ]);

        $comment->load([
            'student:id,name,photo',
            'user:id,name,img',
            'replies.student:id,name,photo',
            'replies.user:id,name,img'
        ]);

        return response()->json([
            'success' => true,
            'data' => $comment
        ], 201);
    }

    public function destroy(PostComment $comment)
    {
        $student = \App\Models\Student::where('username', Auth::user()->username)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Autentikasi siswa gagal'
            ], 403);
        }

        if ($comment->student_id != $student->id) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak diizinkan'
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Komentar dihapus'
        ]);
    }
}
