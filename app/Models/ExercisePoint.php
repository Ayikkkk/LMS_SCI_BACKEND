<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExercisePoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_id',
        'exercise_id',
        'student_id',
        'answer',
        'competence_point',
        'exercise_point',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
