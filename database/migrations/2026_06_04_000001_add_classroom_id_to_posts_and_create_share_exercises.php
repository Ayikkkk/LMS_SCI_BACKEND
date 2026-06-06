<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sinkronisasi struktur database:
 * 1. Tambah classroom_id (nullable) ke tabel posts
 * 2. Buat tabel share_exercises (pivot exercise → serial/classroom)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah classroom_id ke posts jika belum ada
        if (!Schema::hasColumn('posts', 'classroom_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->unsignedInteger('classroom_id')
                    ->nullable()
                    ->after('serial_id');

                $table->foreign('classroom_id')
                    ->references('id')
                    ->on('classrooms')
                    ->nullOnDelete();

                $table->index('classroom_id');
            });
        }

        // 2. Buat tabel share_exercises jika belum ada
        if (!Schema::hasTable('share_exercises')) {
            Schema::create('share_exercises', function (Blueprint $table) {
                $table->unsignedInteger('serial_id');
                $table->unsignedInteger('exercise_id');
                $table->unsignedInteger('classroom_id')->nullable();
                $table->timestamps();

                $table->foreign('serial_id')
                    ->references('id')
                    ->on('serials')
                    ->cascadeOnDelete();

                $table->foreign('exercise_id')
                    ->references('id')
                    ->on('exercises')
                    ->cascadeOnDelete();

                $table->foreign('classroom_id')
                    ->references('id')
                    ->on('classrooms')
                    ->nullOnDelete();

                $table->index(['serial_id', 'exercise_id']);
            });
        }
    }

    public function down(): void
    {
        // Hapus share_exercises
        Schema::dropIfExists('share_exercises');

        // Hapus classroom_id dari posts
        if (Schema::hasColumn('posts', 'classroom_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropForeign(['classroom_id']);
                $table->dropIndex(['classroom_id']);
                $table->dropColumn('classroom_id');
            });
        }
    }
};
