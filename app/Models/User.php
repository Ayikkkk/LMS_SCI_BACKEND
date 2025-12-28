<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'img',
        'login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast attributes automatically
     */
    protected $casts = [
        'login_at' => 'datetime',
    ];

    /**
     * Gunakan username untuk autentikasi login, bukan email
     */
    public function username()
    {
        return 'username';
    }

    /**
     * RELATIONS
     */

    // Guru memiliki banyak siswa (melalui serial & kelas)
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Guru memiliki banyak kelas
    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    // Guru sebagai pembuat meeting
    public function onlineMeetings()
    {
        return $this->hasMany(OnlineMeeting::class, 'user_id', 'id');
    }

    // Jika ada posts (fitur lain)
    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
