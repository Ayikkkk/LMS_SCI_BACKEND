<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_id',
        'student_id',
        'report',
        'img',
    ];

    // Relasi ke Serial
    public function serial()
    {
        return $this->belongsTo(Serial::class, 'serial_id');
    }

    // Relasi ke Student
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
