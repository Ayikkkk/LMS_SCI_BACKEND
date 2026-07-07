<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah unique constraint pada exercise_points(student_id, exercise_id).
 *
 * LANGKAH AMAN:
 * 1. Hapus dulu baris duplikat yang mungkin ada (simpan hanya yang terbaru per pasangan)
 * 2. Drop index biasa yang sudah ada (nama: exercise_points_student_id_exercise_id_index)
 * 3. Buat unique constraint baru
 *
 * Ini adalah migration terpisah dari create table sehingga data production tidak tersentuh
 * jika migration ini di-rollback.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // LANGKAH 1 — Bersihkan duplikat (simpan hanya row terbaru)
        // ============================================================
        // Temukan pasangan (student_id, exercise_id) yang punya lebih dari 1 baris
        $duplicates = DB::table('exercise_points')
            ->select('student_id', 'exercise_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id', 'exercise_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            // Ambil semua ID untuk pasangan ini, urutkan dari terlama ke terbaru
            $ids = DB::table('exercise_points')
                ->where('student_id', $dup->student_id)
                ->where('exercise_id', $dup->exercise_id)
                ->orderBy('id', 'desc')
                ->pluck('id')
                ->toArray();

            // Pertahankan yang terbaru (index 0), hapus sisanya
            $keepId = array_shift($ids);
            if (!empty($ids)) {
                DB::table('exercise_points')
                    ->whereIn('id', $ids)
                    ->delete();
            }
        }

        // ============================================================
        // LANGKAH 2 — Drop index biasa yang sudah ada
        // ============================================================
        Schema::table('exercise_points', function (Blueprint $table) {
            // Drop index yang dibuat di migration awal
            // Nama index: exercise_points_student_id_exercise_id_index (konvensi Laravel)
            $table->dropIndex(['student_id', 'exercise_id']);
        });

        // ============================================================
        // LANGKAH 3 — Buat unique constraint
        // ============================================================
        Schema::table('exercise_points', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'exercise_id'],
                'uq_exercise_points_student_exercise'
            );
        });
    }

    public function down(): void
    {
        Schema::table('exercise_points', function (Blueprint $table) {
            $table->dropUnique('uq_exercise_points_student_exercise');
            // Kembalikan ke index biasa
            $table->index(['student_id', 'exercise_id']);
        });
    }
};
