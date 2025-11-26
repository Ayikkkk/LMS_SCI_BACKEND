<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class LMSSeeder extends Seeder
{
    public function run(): void
    {
        // ===== USERS (Guru) =====
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Guru Matematika',
                'username' => 'guru_mtk',
                'password' => Hash::make('password'),
                'email' => 'guru@sekolah.com',
                'role' => 1,
                'address' => 'Jl. Pendidikan No.1',
                'phone' => '081234567890',
                'img' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== MAPELS =====
        $mapels = [
            'PPKN', 'Matematika', 'Bhs. Indonesia', 'IPA', 'IPS', 'SBDP', 'PJOK',
            'PADBP Islam', 'Bhs. Arab', 'Al-Quran Hadis', 'SKI', 'Fiqih',
            'Akidah Akhlak', 'Bhs. Inggris', 'Bhs. Jawa', 'BTQ', 'Tematik',
            'AKM', 'IPAS', 'Kepercayaan', 'Informatika', 'Kesenian', 'P5'
        ];

        foreach ($mapels as $mapel) {
            DB::table('mapels')->insert([
                'name' => $mapel,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== LESSONS =====
        DB::table('lessons')->insert([
            [
                'id' => 1,
                'mapel_id' => 2,
                'name' => 'Bilangan dan Operasi Hitung',
                'grade' => '5',
                'semester' => 1,
                'category' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== PRODUCTS =====
        DB::table('products')->insert([
            [
                'id' => 1,
                'lesson_id' => 1,
                'name' => 'Paket Belajar Matematika Kelas 5',
                'grade' => '5',
                'grade_category' => 'SD',
                'semester' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== SERIALS =====
        DB::table('serials')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'product_id' => 1,
                'serial' => 'SERIAL12345',
                'paket' => 'A',
                'active' => 'yes',
                'expired_at' => Carbon::now()->addMonths(6),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== CLASSROOMS =====
        DB::table('classrooms')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'name' => 'Kelas 5A',
                'grade' => '5',
                'code' => 'CLS5A2025',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== STUDENTS =====
        DB::table('students')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'user_id' => 1,
                'classroom_id' => 1,
                'name' => 'Reno Saputra',
                'username' => 'reno',
                'password' => Hash::make('reno123'),
                'password_text' => 'reno123',
                'nis' => '2025001',
                'email' => 'reno@student.com',
                'phone' => '08122334455',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== COMPETENCES =====
        DB::table('competences')->insert([
            [
                'id' => 1,
                'lesson_id' => 1,
                'mapel_id' => 2,
                'point' => '3.1',
                'description' => 'Menjelaskan bilangan bulat dan operasi hitung sederhana.',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== EXERCISE TYPES =====
        DB::table('exercise_types')->insert([
            ['id' => 1, 'kode' => 'UH', 'name' => 'Ulangan Harian', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'kode' => 'PTS', 'name' => 'Penilaian Tengah Semester', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'kode' => 'UAS', 'name' => 'Ujian Akhir Semester', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ===== EXERCISE MODELS =====
        $models = ['Pilihan Ganda', 'Pilihan Ganda Banyak', 'Pernyataan', 'Isian', 'Uraian', 'Iya Tidak', 'Argumen'];
        foreach ($models as $m) {
            DB::table('exercise_models')->insert([
                'name' => $m,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ===== EXERCISES =====
        DB::table('exercises')->insert([
            [
                'id' => 1,
                'lesson_id' => 1,
                'serial_id' => 1,
                'exercise_type_id' => 1,
                'title' => 'Latihan Bilangan Bulat',
                'is_admin' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== EXERCISE ITEMS =====
        DB::table('exercise_items')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'competence_id' => 1,
                'exercise_id' => 1,
                'exercise_type_id' => 1,
                'exercise_model_id' => 1,
                'exercise_choice' => 4,
                'exercise_number' => 1,
                'question' => 'Hasil dari 25 + (-10) adalah ...',
                'selection' => json_encode(['15', '35', '-15', '10']),
                'answer' => '15',
                'is_user' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== POSTS =====
        DB::table('posts')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'user_id' => 1,
                'mapel_id' => 2,
                'title' => 'Tugas Operasi Hitung',
                'description' => 'Kerjakan soal latihan operasi hitung di buku paket halaman 20.',
                'slug' => 'tugas-operasi-hitung',
                'category' => 'tugas',
                'is_task' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== TASKS (jawaban siswa) =====
        DB::table('tasks')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'post_id' => 1,
                'student_id' => 1,
                'description' => 'Sudah saya kerjakan di buku tulis.',
                'attachment' => null,
                'point' => '90',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== ONLINE MEETINGS =====
        DB::table('online_meetings')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'classroom_id' => 1,
                'user_id' => 1,
                'title' => 'Kelas Online Matematika',
                'description' => 'Pertemuan pertama belajar operasi hitung',
                'meeting_code' => 'MTKMEET1',
                'meeting_link' => 'https://meet.google.com/abc-defg-hij',
                'platform' => 'Google Meet',
                'start_time' => now()->addDay(),
                'end_time' => now()->addDays(1)->addHour(),
                'status' => 'upcoming',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        // ===== REPORTS =====
        DB::table('reports')->insert([
            [
                'id' => 1,
                'serial_id' => 1,
                'student_id' => 1,
                'report' => 'Hari ini saya belajar operasi hitung bilangan bulat.',
                'img' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
