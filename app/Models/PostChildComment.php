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
        'is_user' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['author_name', 'author_photo'];

    // ========== ATTRIBUTES ========== //
    public function getAuthorNameAttribute()
    {
        if ($this->is_user && $this->user) {
            return $this->user->name;
        } elseif (!$this->is_user && $this->student) {
            return $this->student->name;
        }

        return "Unknown";
    }

    public function getAuthorPhotoAttribute()
    {
        if ($this->is_user && $this->user && $this->user->img) {
            return asset('storage/users/' . $this->user->img);
        }

        if (!$this->is_user && $this->student && $this->student->photo) {
            return asset('storage/students/' . $this->student->photo);
        }

        return null;
    }

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
