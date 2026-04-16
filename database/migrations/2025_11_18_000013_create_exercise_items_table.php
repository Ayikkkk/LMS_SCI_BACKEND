<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('admin_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('competence_id')->nullable();
            $table->unsignedInteger('exercise_id');
            $table->unsignedInteger('exercise_type_id');
            $table->unsignedInteger('exercise_model_id');
            $table->tinyInteger('exercise_choice');
            $table->integer('exercise_number');
            $table->text('question');
            $table->text('selection')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_user')->default(false);
            $table->timestamps();
            $table->foreign('admin_id')->references('id')->on('admins')->nullOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('competence_id')->references('id')->on('competences')->nullOnDelete();
            $table->foreign('exercise_id')->references('id')->on('exercises')->cascadeOnDelete();
            $table->foreign('exercise_type_id')->references('id')->on('exercise_types')->cascadeOnDelete();
            $table->foreign('exercise_model_id')->references('id')->on('exercise_models')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_items');
    }
};
