<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineMeetingParticipant extends Model
{
    use HasFactory;

    protected $table = 'online_meeting_participants';

    protected $fillable = [
        'online_meeting_id',
        'user_id',
        'role',       // teacher | student
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at'   => 'datetime',
    ];

    public $timestamps = true;

    // ================= RELATIONS =================

    public function meeting()
    {
        return $this->belongsTo(
            OnlineMeeting::class,
            'online_meeting_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    // ================= HELPERS =================

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function durationInMinutes(): ?int
    {
        if (!$this->left_at) {
            return null;
        }

        return $this->joined_at->diffInMinutes($this->left_at);
    }
}
