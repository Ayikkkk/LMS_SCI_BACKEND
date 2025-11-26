<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Tambahkan kolom due_date sebagai timestamp yang boleh NULL
            $table->timestamp('due_date')->nullable()->after('embed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            // Hapus kolom due_date jika migrasi di-rollback
            $table->dropColumn('due_date');
        });
    }
};