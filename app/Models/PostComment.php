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
        $baseUrl = rtrim((string) config('app.url'), '/');

        if ($this->is_user && $this->user && $this->user->img) {
            return $baseUrl . '/api/files/users/' . $this->user->img;
        }

        if (!$this->is_user && $this->student && $this->student->photo) {
            $photo = $this->student->photo;
            // Jika sudah full URL, ekstrak relative path-nya
            if (str_starts_with($photo, 'http://') || str_starts_with($photo, 'https://')) {
                $parsed = parse_url($photo, PHP_URL_PATH);
                $relativePath = ltrim(str_replace('/storage/', '', $parsed), '/');
                return $baseUrl . '/api/files/' . $relativePath;
            }
            return $baseUrl . '/api/files/' . $photo;
        }

        return null;
    }

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
