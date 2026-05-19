<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('serial_id')->nullable();
            $table->unsignedInteger('exercise_type_id');
            $table->string('title', 200)->nullable();
            $table->unsignedSmallInteger('time_limit')->nullable(); // waktu pengerjaan dalam menit, null = tidak ada batas
            $table->boolean('is_admin')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
            $table->foreign('serial_id')->references('id')->on('serials')->nullOnDelete();
            $table->foreign('exercise_type_id')->references('id')->on('exercise_types')->cascadeOnDelete();
            $table->index(['lesson_id', 'serial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
