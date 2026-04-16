<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_meetings', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('serial_id');
            $table->unsignedInteger('classroom_id');
            $table->unsignedInteger('user_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('meeting_code', 50)->unique();
            $table->text('meeting_link')->nullable();
            $table->string('platform', 50)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['upcoming', 'live', 'ended', 'cancelled'])->default('upcoming');
            $table->timestamps();
            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->index('classroom_id');
            $table->index('user_id');
            $table->index('start_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_meetings');
    }
};
