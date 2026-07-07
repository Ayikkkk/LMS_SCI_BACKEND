<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostChildComment;
use App\Models\PostComment;

class PostChildCommentController extends Controller
{
    public function store(Request $request, PostComment $comment)
    {
        $request->validate(['message' => 'required']);

        // Gunakan $request->user() — tidak perlu query DB tambahan
        $student = $request->user();

        $reply = PostChildComment::create([
            'post_comment_id' => $comment->id,
            'student_id'      => $student->id,
            'message'         => $request->message,
            'is_user'         => 0
        ]);

        $reply->load(['student:id,name,photo', 'user:id,name,img']);

        return response()->json([
            'success' => true,
            'data' => $reply
        ], 201);
    }

    public function destroy(Request $request, PostChildComment $reply)
    {
        $student = $request->user();

        if ($reply->student_id !== $student->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        $reply->delete();

        return response()->json(['success' => true, 'message' => 'Balasan dihapus']);
    }

    public function update(Request $request, PostChildComment $reply)
    {
        $request->validate(['message' => 'required']);

        $student = $request->user();

        if ($reply->student_id != $student->id) {
            return response()->json(['success' => false, 'message' => 'Tidak diizinkan'], 403);
        }

        $reply->update(['message' => $request->message]);

        $reply->load(['student:id,name,photo', 'user:id,name,img']);

        return response()->json([
            'success' => true,
            'message' => 'Balasan diperbarui',
            'data'    => $reply
        ]);
    }
}
