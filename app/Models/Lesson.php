<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'mapel_id',
        'name',
        'grade',
        'semester',
        'category'
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'lesson_id');
    }
}
