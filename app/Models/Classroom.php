<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['serial_id', 'name', 'grade', 'code'];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // teacher() dihapus: kolom user_id tidak ada di tabel classrooms (tidak di migration).
    // Relasi guru-siswa tersimpan di model Student via Student::guru() (FK user_id di students).
    // Menambah with('teacher') pada query akan menyebabkan query yang selalu return null.
}
