<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'materi_id',
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
