<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShareExercise extends Model
{
    protected $table = 'share_exercises';

    protected $fillable = [
        'serial_id',
        'exercise_id',
        'classroom_id',
    ];

    // =========== RELATIONS ===========

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
}
