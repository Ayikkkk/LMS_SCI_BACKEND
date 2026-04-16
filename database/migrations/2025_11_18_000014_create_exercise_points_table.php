<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_points', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('serial_id');
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('student_id');
            $table->text('answer');
            $table->text('competence_point')->nullable();
            $table->string('exercise_point', 3)->nullable();
            $table->timestamps();
            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->index(['student_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_points');
    }
};
