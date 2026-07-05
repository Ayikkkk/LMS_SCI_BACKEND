<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan index pada kolom yang sering diquery untuk performa banyak user.
 * Index ini mengurangi full table scan saat query WHERE/JOIN.
 */
return new class extends Migration
{
    public function up(): void
    {
        // exercise_items: sering di-query WHERE exercise_id = ?
        Schema::table('exercise_items', function (Blueprint $table) {
            if (!$this->indexExists('exercise_items', 'exercise_items_exercise_id_index')) {
                $table->index('exercise_id', 'exercise_items_exercise_id_index');
            }
        });

        // exercise_points: sering di-query WHERE student_id = ? AND exercise_id = ?
        Schema::table('exercise_points', function (Blueprint $table) {
            if (!$this->indexExists('exercise_points', 'exercise_points_student_exercise_index')) {
                $table->index(['student_id', 'exercise_id'], 'exercise_points_student_exercise_index');
            }
        });

        // tasks: sering di-query WHERE student_id = ? AND post_id = ?
        Schema::table('tasks', function (Blueprint $table) {
            if (!$this->indexExists('tasks', 'tasks_student_post_index')) {
                $table->index(['student_id', 'post_id'], 'tasks_student_post_index');
            }
        });

        // posts: sering di-query WHERE serial_id = ? AND is_task = ? AND classroom_id = ?
        Schema::table('posts', function (Blueprint $table) {
            if (!$this->indexExists('posts', 'posts_serial_task_classroom_index')) {
                $table->index(['serial_id', 'is_task', 'classroom_id'], 'posts_serial_task_classroom_index');
            }
        });

        // online_meetings: sering di-query WHERE classroom_id = ? AND start_time BETWEEN ?
        Schema::table('online_meetings', function (Blueprint $table) {
            if (!$this->indexExists('online_meetings', 'online_meetings_classroom_start_index')) {
                $table->index(['classroom_id', 'start_time'], 'online_meetings_classroom_start_index');
            }
        });

        // share_exercises: sering di-query WHERE exercise_id IN (?) AND serial_id = ?
        Schema::table('share_exercises', function (Blueprint $table) {
            if (!$this->indexExists('share_exercises', 'share_exercises_exercise_serial_index')) {
                $table->index(['exercise_id', 'serial_id'], 'share_exercises_exercise_serial_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('exercise_items', function (Blueprint $table) {
            $table->dropIndexIfExists('exercise_items_exercise_id_index');
        });
        Schema::table('exercise_points', function (Blueprint $table) {
            $table->dropIndexIfExists('exercise_points_student_exercise_index');
        });
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndexIfExists('tasks_student_post_index');
        });
        Schema::table('posts', function (Blueprint $table) {
            $table->dropIndexIfExists('posts_serial_task_classroom_index');
        });
        Schema::table('online_meetings', function (Blueprint $table) {
            $table->dropIndexIfExists('online_meetings_classroom_start_index');
        });
        Schema::table('share_exercises', function (Blueprint $table) {
            $table->dropIndexIfExists('share_exercises_exercise_serial_index');
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = \Illuminate\Support\Facades\DB::select(
                "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                [$indexName]
            );
            return count($indexes) > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
};
