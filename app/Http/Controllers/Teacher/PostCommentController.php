<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PostComment;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PostCommentController extends Controller
{
    private function getTeacher()
    {
        return Auth::user() ?? User::first();
    }

    public function index(Post $post)
    {
        $comments = PostComment::with([
            'student:id,name,photo',
            'user:id,name,img',
            'replies.student:id,name,photo',
            'replies.user:id,name,img'
        ])
        ->where('post_id', $post->id)
        ->orderBy('created_at', 'ASC')
        ->get();

        return view('teacher.comments.index', compact('post', 'comments'));
    }

    public function store(Request $request, Post $post)
    {
        $request->validate(['message' => 'required']);
        $teacher = $this->getTeacher();

        PostComment::create([
            'post_id' => $post->id,
            'user_id' => $teacher->id,
            'message' => $request->message,
            'is_user' => 1
        ]);

        return back()->with('success', 'Komentar guru ditambahkan');
    }

    public function destroy(PostComment $comment)
    {
        // 🔥 Guru bisa hapus komentar siapa saja (moderator)

        // Hapus balasan juga
        $comment->replies()->delete();
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus');
    }
}
