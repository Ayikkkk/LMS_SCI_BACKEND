<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseType extends Model
{
    protected $table = 'exercise_types';
    public $timestamps = false;
    protected $fillable = ['kode', 'name'];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'exercise_type_id');
    }
}
