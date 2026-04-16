<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class Student extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

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
        'phone',
        'photo',
        'absen_number',
        'classroom_id',
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
        'id'           => 'integer',
        'classroom_id' => 'integer',
        'user_id'      => 'integer',
        'serial_id'    => 'integer',
        'absen_number' => 'integer',
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

    // Accessor — returns /storage/... relative path, never a full URL.
    // Use request()->getSchemeAndHttpHost() + this value to build full URL in controllers.
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo)) return null;

        if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
            // Legacy full URL — return only the /storage/... path
            return parse_url($this->photo, PHP_URL_PATH);
        }

        return '/storage/' . $this->photo;
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
