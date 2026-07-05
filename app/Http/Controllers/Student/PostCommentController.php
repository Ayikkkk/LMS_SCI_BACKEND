<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostComment;
use App\Models\Post;

class PostCommentController extends Controller
{
    public function index(Post $post)
    {
        $comments = PostComment::with(['student', 'user', 'replies.student', 'replies.user'])
            ->where('post_id', $post->id)
            ->orderBy('created_at', 'ASC')
            ->get();

        return response()->json([
            'success'  => true,
            'comments' => $comments
        ]);
    }

    public function store(Request $request, Post $post)
    {
        $request->validate(['message' => 'required']);

        // Gunakan $request->user() langsung — tidak perlu query DB lagi
        $student = $request->user();

        $comment = PostComment::create([
            'post_id'    => $post->id,
            'student_id' => $student->id,
            'message'    => $request->message,
            'code'       => uniqid('CMT'),
            'is_user'    => 0
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

    public function destroy(Request $request, PostComment $comment)
    {
        $student = $request->user();

        if ($comment->student_id != $student->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true, 'message' => 'Komentar dihapus']);
    }

    public function update(Request $request, PostComment $comment)
    {
        $request->validate(['message' => 'required']);

        $student = $request->user();

        if ($comment->student_id != $student->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        $comment->update(['message' => $request->message]);

        $comment->load([
            'student:id,name,photo',
            'user:id,name,img',
            'replies.student:id,name,photo',
            'replies.user:id,name,img'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Komentar berhasil diperbarui',
            'data'    => $comment
        ]);
    }
}
