<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, SoftDeletes;

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

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
    }
}
