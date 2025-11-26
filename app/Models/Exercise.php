<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'serial_id',
        'exercise_type_id',
        'title',
        'is_admin',
    ];

    // Relasi ke soal-soalnya
    public function items()
    {
        return $this->hasMany(ExerciseItem::class, 'exercise_id');
    }

    // Relasi ke nilai siswa
    public function points()
    {
        return $this->hasMany(ExercisePoint::class, 'exercise_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }
}
