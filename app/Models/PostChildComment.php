<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostChildComment extends Model
{
    protected $table = 'post_child_comments';

    protected $fillable = [
        'post_comment_id',
        'user_id',
        'student_id',
        'message',
        'is_user',
    ];

    protected $casts = [
        'is_user'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * $appends DINONAKTIFKAN — sama seperti PostComment.
     * Relasi di-eager load di controller dengan kolom terbatas.
     */
    // protected $appends = ['author_name', 'author_photo'];

    // ========== RELATIONSHIPS ========== //
    public function comment()
    {
        return $this->belongsTo(PostComment::class, 'post_comment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
