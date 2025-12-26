<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'classroom_id',
        'user_id', // guru
        'title',
        'description',
        'meeting_code',
        'start_time',
        'end_time',
        'status',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function participants()
    {
        return $this->hasMany(
            OnlineMeetingParticipant::class,
            'online_meeting_id', // Foreign Key di participant
            'id' // Primary Key di online_meetings
        );
    }
}
