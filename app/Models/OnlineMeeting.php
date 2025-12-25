<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'user_id',        // guru
        'title',
        'description',
        'meeting_code',
        'start_time',
        'end_time',
        'status',
    ];

    // Relasi ke kelas
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    // Relasi ke guru
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke peserta (siswa & guru)
    public function participants()
    {
        return $this->hasMany(OnlineMeetingParticipant::class);
    }
}
