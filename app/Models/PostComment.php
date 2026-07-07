<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    protected $table = 'post_comments';

    protected $fillable = [
        'post_id',
        'user_id',
        'student_id',
        'message',
        'code',
        'is_user',
    ];

    protected $casts = [
        'is_user'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * $appends DINONAKTIFKAN — accessor author_name/author_photo dulu menyebabkan
     * N+1 karena akses relasi $this->user / $this->student secara lazy per record.
     *
     * Pengganti: eager load relasi di controller dengan kolom terbatas,
     * lalu Flutter membaca field student.name / user.name langsung dari JSON.
     */
    // protected $appends = ['author_name', 'author_photo'];

    // ========== RELATIONSHIPS ========== //
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function replies()
    {
        return $this->hasMany(PostChildComment::class, 'post_comment_id')
            ->orderBy('created_at', 'ASC');
    }
}
