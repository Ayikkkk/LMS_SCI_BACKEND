<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mapel extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'mapel_id');
    }
}
