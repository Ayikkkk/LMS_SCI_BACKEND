<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'serial_id',
        'post_id',
        'student_id',
        'description',
        'attachment',
        'point',
    ];

    // Tugas milik satu siswa
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Tugas dikaitkan dengan satu postingan (post/tugas guru)
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    // Setiap tugas juga terkait ke serial (paket)
    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }
}
