<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        // =====================
        // ADMINS
        // =====================
        DB::table('admins')->insert([
            ['id' => 1, 'name' => 'Fahri Kurniawan', 'username' => 'fahri', 'password' => '$2y$10$cP/SinrmMHi5ZxApNXHeL.RjMod8ikpB63QJ/MGdCGTo28ZmX6t7O', 'role' => 7, 'date_in' => '2025-10-13', 'position' => 'Administrator', 'phone' => '08467377832', 'img' => null, 'login_at' => '2025-12-18 12:46:10', 'created_at' => '2025-12-18 12:46:10', 'updated_at' => '2025-12-18 12:47:44'],
        ]);

        // =====================
        // MAPELS
        // =====================
        DB::table('mapels')->insert([
            ['id' => 1,  'name' => 'PPKN',          'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 2,  'name' => 'Matematika',     'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 3,  'name' => 'Bhs. Indonesia', 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 4,  'name' => 'IPA',            'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 5,  'name' => 'IPS',            'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 6,  'name' => 'SBDP',           'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 7,  'name' => 'PJOK',           'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 8,  'name' => 'PADBP Islam',    'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 9,  'name' => 'Bhs. Arab',      'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 10, 'name' => 'Al-Quran Hadis', 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 11, 'name' => 'SKI',            'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 12, 'name' => 'Fiqih',          'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 13, 'name' => 'Akidah Akhlak',  'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 14, 'name' => 'Bhs. Inggris',   'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 15, 'name' => 'Bhs. Jawa',      'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 16, 'name' => 'BTQ',            'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 17, 'name' => 'Tematik',        'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 18, 'name' => 'AKM',            'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 19, 'name' => 'IPAS',           'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 20, 'name' => 'Kepercayaan',    'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 21, 'name' => 'Informatika',    'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 22, 'name' => 'Kesenian',       'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 23, 'name' => 'P5',             'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
        ]);

        // =====================
        // LESSONS
        // =====================
        DB::table('lessons')->insert([
            ['id' => 1, 'mapel_id' => 2, 'name' => 'Kurikulum Merdeka Matematika',      'grade' => 'V', 'semester' => 1, 'category' => 1, 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 2, 'mapel_id' => 3, 'name' => 'Kurikulum Merdeka Bahasa Indonesia', 'grade' => 'V', 'semester' => 1, 'category' => 1, 'created_at' => '2025-12-09 06:17:45', 'updated_at' => '2025-12-09 06:17:45'],
        ]);

        // =====================
        // PRODUCTS
        // =====================
        DB::table('products')->insert([
            ['id' => 1, 'lesson_id' => 1, 'name' => 'Paket SD Kelas V', 'grade' => 'V', 'grade_category' => 'SD', 'semester' => '1', 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
        ]);

        // =====================
        // USERS (Guru)
        // =====================
        DB::table('users')->insert([
            ['id' => 1, 'name' => 'Fahri Kurniawan S.Kom', 'username' => 'guru_mtk', 'password' => '$2y$12$qc.OfrU1CVu3TsoH5DwBrugiHi7xYpY3XLy3cI339m8ZJQQZr5q0W', 'email' => 'guru@sekolah.com', 'role' => 1, 'address' => 'Jl. Pendidikan No.1', 'phone' => '081234567890', 'img' => null, 'login_at' => null, 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-12-26 07:46:03'],
            ['id' => 2, 'name' => 'Guru Kedua', 'username' => 'gurukedua', 'password' => '$2y$10$UxSJr9TFHj3fYTGTrkrGRuO6IbCJMZdu80NAbFBgMiMKCvQJcKR6i', 'email' => 'guru2@example.com', 'role' => 1, 'address' => 'Sekolah Indonesia', 'phone' => '081234567890', 'img' => null, 'login_at' => '2025-12-26 06:49:15', 'created_at' => '2025-12-26 06:49:15', 'updated_at' => '2025-12-26 06:49:15'],
        ]);

        // =====================
        // SERIALS
        // =====================
        DB::table('serials')->insert([
            ['id' => 1, 'user_id' => 1, 'product_id' => 1, 'serial' => 'SERIAL12345', 'paket' => '4', 'active' => 'yes', 'expired_at' => '2026-05-18 08:26:56', 'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
        ]);

        // =====================
        // CLASSROOMS
        // =====================
        DB::table('classrooms')->insert([
            ['id' => 1, 'serial_id' => 1, 'name' => '5A', 'grade' => 'V', 'code' => 'CLS5A2025',   'created_at' => '2025-11-18 08:26:56', 'updated_at' => '2025-11-18 08:26:56'],
            ['id' => 2, 'serial_id' => 1, 'name' => '5B', 'grade' => 'V', 'code' => 'CLASS5B2025', 'created_at' => '2025-11-26 16:31:41', 'updated_at' => '2025-11-26 16:31:41'],
        ]);

        // =====================
        // STUDENTS
        // =====================
        DB::table('students')->insert([
            ['id' => 1, 'serial_id' => 1, 'user_id' => 1, 'classroom_id' => 1, 'name' => 'Reno Saputra',       'username' => 'reno', 'password' => '$2y$12$en4h66VY0JPVc1bXjwf/We.nxW9goxBSOgBjeY2Vq/ma80K3AtKDW', 'password_text' => 'reno123',    'nis' => '2025001',    'absen_number' => 1, 'email' => 'reno@student.com',    'phone' => '08122334455', 'photo' => 'students/twz2bJw0BcLggnXfcBpOn6r91whcDrYU2K0V3fQT.jpg', 'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2026-02-25 12:56:09'],
            ['id' => 2, 'serial_id' => 1, 'user_id' => 1, 'classroom_id' => 1, 'name' => 'Budi Santoso Raul',  'username' => 'budi', 'password' => '$2y$12$RUsCa0Itl4t8QFGqr3/E8OM8kJMuIWTgP61hMKOMBsWhZw0zrkb4S', 'password_text' => 'budi12345', 'nis' => '8983978923', 'absen_number' => 2, 'email' => 'budiraul34@gmail.com', 'phone' => '08395566877', 'photo' => 'students/GbXqFMLWPh8MjuyhVNtULB6oTnLujZuKBpDH2aZC.jpg', 'created_at' => '2025-11-22 10:21:15', 'updated_at' => '2026-03-05 22:45:31'],
            ['id' => 3, 'serial_id' => 1, 'user_id' => 1, 'classroom_id' => 2, 'name' => 'Heri Kopling',       'username' => 'heri', 'password' => '$2y$12$c9TMGJ4HhKqvk6rqqX1OB.B9AYCWuCe.OQoyQQTLM0PGRRwfXD6sS', 'password_text' => 'heri123',   'nis' => '83926362',   'absen_number' => null, 'email' => 'heri@gmail.com',      'phone' => '08382263548', 'photo' => null, 'created_at' => '2025-11-26 16:33:06', 'updated_at' => '2025-11-26 16:33:06'],
        ]);

        // =====================
        // COMPETENCES
        // =====================
        DB::table('competences')->insert([
            ['id' => 1, 'lesson_id' => 1, 'mapel_id' => 2, 'point' => '3.1', 'description' => 'Menjelaskan bilangan bulat dan operasi hitung sederhana.', 'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 2, 'lesson_id' => 2, 'mapel_id' => 3, 'point' => '3.1', 'description' => 'Siswa mampu membaca dengan lancar',                         'created_at' => '2025-12-09 06:21:04', 'updated_at' => '2025-12-09 06:21:04'],
        ]);

        // =====================
        // EXERCISE TYPES
        // =====================
        DB::table('exercise_types')->insert([
            ['id' => 1, 'kode' => 'UH',   'name' => 'Ulangan Harian',                        'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 2, 'kode' => 'PTS',  'name' => 'Penilaian Tengah Semester',              'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 3, 'kode' => 'UAS',  'name' => 'Ujian Akhir Semester',                   'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 4, 'kode' => 'AKM',  'name' => 'Asesmen Kompetensi Minimum',             'created_at' => '2025-12-09 05:20:10', 'updated_at' => '2025-12-09 05:20:10'],
            ['id' => 5, 'kode' => 'ASPD', 'name' => 'Asesmen Standardisasi Pendidikan Daerah','created_at' => '2025-12-09 05:21:06', 'updated_at' => '2025-12-09 05:21:06'],
        ]);

        // =====================
        // EXERCISE MODELS
        // =====================
        DB::table('exercise_models')->insert([
            ['id' => 1, 'name' => 'Pilihan Ganda',       'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 2, 'name' => 'Pilihan Ganda Banyak','created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 3, 'name' => 'Pernyataan',          'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 4, 'name' => 'Isian',               'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 5, 'name' => 'Uraian',              'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 6, 'name' => 'Iya Tidak',           'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 7, 'name' => 'Argumen',             'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
        ]);

        // =====================
        // EXERCISES
        // =====================
        DB::table('exercises')->insert([
            ['id' => 1, 'lesson_id' => 1, 'serial_id' => 1, 'exercise_type_id' => 1, 'title' => 'Latihan Bilangan Bulat',   'is_admin' => 1, 'created_at' => '2025-11-18 08:26:57', 'updated_at' => '2025-11-18 08:26:57'],
            ['id' => 2, 'lesson_id' => 2, 'serial_id' => 1, 'exercise_type_id' => 1, 'title' => 'Pengenalan Kata Kerja',    'is_admin' => 1, 'created_at' => '2025-12-09 06:22:43', 'updated_at' => '2025-12-09 06:22:43'],
            ['id' => 3, 'lesson_id' => 1, 'serial_id' => 1, 'exercise_type_id' => 2, 'title' => 'Latihan Bilangan Kuadrat', 'is_admin' => 1, 'created_at' => '2025-12-09 08:08:24', 'updated_at' => '2025-12-09 08:08:24'],
            ['id' => 4, 'lesson_id' => 2, 'serial_id' => 1, 'exercise_type_id' => 2, 'title' => 'Macam Kosa Kata',          'is_admin' => 1, 'created_at' => '2025-12-18 12:41:12', 'updated_at' => '2025-12-18 12:41:12'],
            ['id' => 5, 'lesson_id' => 2, 'serial_id' => 1, 'exercise_type_id' => 1, 'title' => 'Ulangan Kata Dasar',       'is_admin' => 1, 'created_at' => '2026-01-02 16:30:01', 'updated_at' => '2026-01-02 16:30:01'],
            ['id' => 6, 'lesson_id' => 2, 'serial_id' => 1, 'exercise_type_id' => 4, 'title' => 'Soal AKM',                 'is_admin' => 1, 'created_at' => '2026-02-25 08:32:32', 'updated_at' => '2026-02-25 08:32:32'],
        ]);
    }
}
