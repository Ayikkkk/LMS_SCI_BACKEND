<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Tambahkan index pada tabel reports untuk query yang sering digunakan.
 *
 * Query yang dioptimasi:
 *
 * 1. checkToday() — dipanggil setiap siswa buka halaman laporan:
 *    WHERE student_id = ? AND DATE(created_at) = ?
 *    → Composite index (student_id, created_at) memungkinkan MySQL scan
 *      hanya baris milik siswa tersebut, bukan full table scan
 *
 * 2. index() — WHERE student_id = ? ORDER BY id DESC
 *    → Prefix (student_id) dari composite index sudah cukup untuk ini
 *
 * 3. show() — WHERE id = ? AND student_id = ?
 *    → id adalah PK (sudah diindex), student_id sebagai filter sekunder
 *      cukup dilayani oleh prefix composite index
 *
 * Trade-off INSERT:
 *    Setiap INSERT ke reports akan update 1 index tambahan.
 *    Laporan harian = paling banyak 1 INSERT per siswa per hari —
 *    overhead ini sangat kecil dan tidak signifikan.
 *
 * Migration ini aman di-rollback (dropIndex tidak merusak data).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Cek apakah index sudah ada sebelum menambah (idempotent)
            $indexes = $this->getIndexNames();

            // Composite index (student_id, created_at) melayani:
            // - checkToday: WHERE student_id = ? AND DATE(created_at) = ?
            // - index():    WHERE student_id = ? ORDER BY id DESC
            if (!in_array('reports_student_created_index', $indexes)) {
                $table->index(
                    ['student_id', 'created_at'],
                    'reports_student_created_index'
                );
            }
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $indexes = $this->getIndexNames();

            if (in_array('reports_student_created_index', $indexes)) {
                $table->dropIndex('reports_student_created_index');
            }
        });
    }

    private function getIndexNames(): array
    {
        try {
            $rows = DB::select("SHOW INDEX FROM `reports`");
            return array_map(fn($r) => $r->Key_name, $rows);
        } catch (\Exception $e) {
            return [];
        }
    }
};
