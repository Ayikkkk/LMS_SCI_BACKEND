<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competences', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lesson_id');
            $table->unsignedInteger('mapel_id');
            $table->string('point', 10);
            $table->text('description');
            $table->timestamps();
            $table->foreign('lesson_id')->references('id')->on('lessons')->cascadeOnDelete();
            $table->foreign('mapel_id')->references('id')->on('mapels')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competences');
    }
};
