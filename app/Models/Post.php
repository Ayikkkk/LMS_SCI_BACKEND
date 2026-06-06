<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    // Tambahkan 'due_date' ke fillable
    protected $fillable = [
        'serial_id',
        'classroom_id',
        'user_id',
        'mapel_id',
        'title',
        'description',
        'link',
        'slug',
        'is_task',
        'due_date',
        'attachment',
        'embed',
        'category',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    /**
     * Relasi ke Guru (Teacher/User).
     */
    public function teacher()
    {
        // Asumsi Model User digunakan untuk Guru
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Mata Pelajaran (Mapel).
     * 💡 Diperlukan oleh PostController untuk mengambil subject_name.
     */
    public function mapel()
    {
        // Asumsi Anda memiliki Model Mapel yang mewakili mata pelajaran
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function tasks()
    {
        return $this->hasOne(Task::class, 'post_id', 'id');
    }
}