<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration untuk database log (mysql_log connection).
 * Jalankan dengan: php artisan migrate --database=mysql_log --path=database/migrations/log
 */
return new class extends Migration
{
    protected $connection = 'mysql_log';

    public function up(): void
    {
        Schema::connection('mysql_log')->create('quiz_activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('exercise_id');
            $table->string('event_type', 100);
            $table->integer('duration_seconds')->nullable();
            $table->boolean('suspicious_flag')->default(false);
            $table->string('device_info', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('student_id');
            $table->index('exercise_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::connection('mysql_log')->dropIfExists('quiz_activity_logs');
    }
};
