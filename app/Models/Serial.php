<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serial extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'serial',
        'paket',
        'active',
        'expired_at',
    ];

    // Serial milik satu guru (atau admin)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Serial dimiliki oleh banyak siswa
    public function students()
    {
        return $this->hasMany(Student::class, 'serial_id');
    }

    // Serial punya banyak tugas
    public function tasks()
    {
        return $this->hasMany(Task::class, 'serial_id');
    }

    // Serial punya banyak laporan harian
    public function reports()
    {
        return $this->hasMany(Report::class, 'serial_id');
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'serial_id');
    }
}
