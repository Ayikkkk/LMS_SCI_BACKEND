<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;


class Student extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [ 'name', 'username', 'email', 'password','plain_password','photo','absen_number','class_room_id','user_id','serial_id'];
    protected $hidden = ['password', 'remember_token'];

    public function guru()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

}