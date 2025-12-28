<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_id',
        'classroom_id',
        'user_id', // guru sebagai host
        'title',
        'description',
        'meeting_code',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    // ================= RELATIONS =================

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    /**
     * Semua participant (guru + siswa)
     */
    public function participants()
    {
        return $this->hasMany(
            OnlineMeetingParticipant::class,
            'online_meeting_id',
            'id'
        );
    }

    /**
     * Khusus siswa
     */
    public function studentParticipants()
    {
        return $this->participants()
            ->where('role', 'student');
    }

    /**
     * Khusus guru (host)
     */
    public function teacherParticipant()
    {
        return $this->participants()
            ->where('role', 'teacher')
            ->first();
    }
}
