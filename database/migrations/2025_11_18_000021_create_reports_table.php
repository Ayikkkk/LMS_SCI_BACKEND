<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('serial_id');
            $table->unsignedInteger('student_id');
            $table->text('report');
            $table->string('img', 255)->nullable();
            $table->timestamps();
            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
