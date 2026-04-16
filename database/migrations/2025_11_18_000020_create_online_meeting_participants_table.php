<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('online_meeting_participants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('online_meeting_id');
            $table->unsignedInteger('user_id');
            $table->enum('role', ['teacher', 'student']);
            $table->dateTime('joined_at');
            $table->dateTime('left_at')->nullable();
            $table->timestamps();
            $table->foreign('online_meeting_id')->references('id')->on('online_meetings')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->unique(['online_meeting_id', 'user_id'], 'uniq_meeting_user');
            $table->index('online_meeting_id', 'idx_meeting');
            $table->index('user_id', 'idx_user');
            $table->index('role', 'idx_role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('online_meeting_participants');
    }
};
