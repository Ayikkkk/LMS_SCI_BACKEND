<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id', // varchar — stores JSON array of lesson IDs e.g. "[1,2,3]"
        'name',
        'grade',
        'grade_category',
        'semester',
    ];

    // Relasi ke Serial
    public function serials()
    {
        return $this->hasMany(Serial::class, 'product_id');
    }
}
