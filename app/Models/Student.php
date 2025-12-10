<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // Jika nama tabel bukan plural default, tambahkan protected $table = 'students';
    // protected $table = 'students';

    /**
     * Kolom yang boleh di-mass assign
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'password_text',
        'phone',
        'photo',
        'absen_number',
        'classroom_id', // sesuaikan dengan kolom di DB
        'user_id',
        'serial_id',
        'nis',
    ];

    /**
     * Sembunyikan field sensitif
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casts (tambahan jika diperlukan)
     */
    protected $casts = [
        'id' => 'int',
        'classroom_id' => 'int',
        'user_id' => 'int',
        'serial_id' => 'int',
    ];

    /**
     * Attribute tambahan yang akan otomatis ditambahkan ke array/json output
     * (mis. $student->photo_url)
     */
    protected $appends = [
        'photo_url',
    ];

    /**
     * RELATIONS
     */

    // Guru (penanggung jawab) — 'user_id' mengarah ke tabel users (model User)
    public function guru()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    // Classroom relation (sesuaikan model namespace jika beda)
    public function classroom()
    {
        return $this->belongsTo(\App\Models\Classroom::class, 'classroom_id');
    }

    // Serial relation (jika ada)
    public function serial()
    {
        return $this->belongsTo(\App\Models\Serial::class, 'serial_id');
    }

    /**
     * MUTATORS / ACCESSORS
     */

    // Hash password otomatis jika diset via $student->password = 'plain';
    public function setPasswordAttribute($value)
    {
        if (empty($value)) {
            return;
        }

        // Jika nilai sudah bcrypt (kurang pasti) — opsi sederhana:
        // tapi safer untuk selalu hash (kecuali sudah ter-hash)
        // Di sini kita cek panjang hash bcrypt (60) dan awalan $2y$/$2a$
        if (is_string($value) && (Str::startsWith($value, '$2y$') || Str::startsWith($value, '$2a$')) && strlen($value) === 60) {
            $this->attributes['password'] = $value;
        } else {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    // Accessor untuk memberikan URL penuh photo
    public function getPhotoUrlAttribute()
    {
        if (empty($this->photo)) return null;

        // Storage::url($this->photo) biasanya mengembalikan '/storage/...'
        // url(...) membuatnya absolute URL berdasarkan APP_URL
        try {
            return url(Storage::url($this->photo));
        } catch (\Throwable $e) {
            // fallback ke nilai langsung (jika sudah URL)
            return $this->photo;
        }
    }

    /**
     * Optional helper: return simplified array for API responses
     */
    public function toPublicArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'phone' => $this->phone,
            'nis' => $this->nis,
            'classroom_id' => $this->classroom_id,
            'className' => optional($this->classroom)->name ?? $this->class_name ?? null,
            'photo' => $this->photo_url, // sudah full url via accessor
            'guru' => $this->guru ? [
                'id' => $this->guru->id,
                'name' => $this->guru->name,
                'email' => $this->guru->email,
                'phone' => $this->guru->phone ?? null,
            ] : null,
        ];
    }
}
