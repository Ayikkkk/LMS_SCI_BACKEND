<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostChildComment;
use App\Models\PostComment;
use Illuminate\Support\Facades\Auth;

class PostChildCommentController extends Controller
{
    private function getTeacher()
    {
        $teacher = Auth::user();
        if (!$teacher) abort(401, 'Unauthenticated.');
        return $teacher;
    }

    public function store(Request $request, PostComment $comment)
    {
        $request->validate(['message' => 'required']);
        $teacher = $this->getTeacher();

        PostChildComment::create([
            'post_comment_id' => $comment->id,
            'user_id' => $teacher->id,
            'message' => $request->message,
            'is_user' => 1
        ]);

        return back()->with('success', 'Balasan guru ditambahkan');
    }

    public function destroy(PostChildComment $reply)
    {
        // 🔥 Guru dapat menghapus balasan siapapun
        $reply->delete();

        return back()->with('success', 'Balasan berhasil dihapus');
    }
}
