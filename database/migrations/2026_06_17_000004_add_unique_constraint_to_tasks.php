<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah unique constraint pada tasks(student_id, post_id).
 *
 * Langkah aman:
 * 1. Hapus baris duplikat (simpan yang terbaru per pasangan)
 * 2. Drop index biasa yang sudah ada
 * 3. Buat unique constraint
 *
 * tasks menggunakan softDeletes — duplikat di baris yang sudah deleted
 * tidak perlu dihapus karena unique constraint memperbolehkan NULL deleted_at
 * untuk beberapa baris yang sama jika MySQL mengabaikan soft deleted rows.
 * Namun untuk keamanan, kita bersihkan semua duplikat termasuk yang deleted.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ============================================================
        // LANGKAH 1 — Bersihkan duplikat (simpan hanya row terbaru)
        // ============================================================
        $duplicates = DB::table('tasks')
            ->select('student_id', 'post_id', DB::raw('COUNT(*) as cnt'))
            ->groupBy('student_id', 'post_id')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $ids = DB::table('tasks')
                ->where('student_id', $dup->student_id)
                ->where('post_id', $dup->post_id)
                ->orderBy('id', 'desc')
                ->pluck('id')
                ->toArray();

            $keepId = array_shift($ids);
            if (!empty($ids)) {
                // Force delete termasuk soft deleted
                DB::table('tasks')->whereIn('id', $ids)->delete();
            }
        }

        // ============================================================
        // LANGKAH 2 — Drop index biasa
        // ============================================================
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'post_id']);
        });

        // ============================================================
        // LANGKAH 3 — Buat unique constraint
        // ============================================================
        Schema::table('tasks', function (Blueprint $table) {
            $table->unique(
                ['student_id', 'post_id'],
                'uq_tasks_student_post'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropUnique('uq_tasks_student_post');
            $table->index(['student_id', 'post_id']);
        });
    }
};
