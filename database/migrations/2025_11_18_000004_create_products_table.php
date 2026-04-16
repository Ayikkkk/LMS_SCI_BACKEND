<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->string('name', 50);
            $table->string('grade', 50)->nullable();
            $table->string('grade_category', 100);
            $table->string('semester', 50)->nullable();
            $table->timestamps();
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
