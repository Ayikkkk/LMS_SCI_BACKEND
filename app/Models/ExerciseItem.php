<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'user_id',
        'competence_id',
        'exercise_id',
        'exercise_type_id',
        'exercise_model_id',
        'exercise_choice',
        'exercise_number',
        'question',
        'selection',
        'answer',
        'is_user',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }
}
