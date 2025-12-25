<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineMeetingParticipant extends Model
{
    protected $fillable = [
        'online_meeting_id',
        'user_id',
        'role',
        'joined_at',
        'left_at',
    ];

    public function meeting()
    {
        return $this->belongsTo(OnlineMeeting::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
