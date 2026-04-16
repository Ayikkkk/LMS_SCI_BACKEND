<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_child_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('post_comment_id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('student_id')->nullable();
            $table->text('message');
            $table->boolean('is_user')->default(false);
            $table->timestamps();
            $table->foreign('post_comment_id')->references('id')->on('post_comments')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->nullOnDelete();
            $table->index('post_comment_id');
            $table->index('user_id');
            $table->index('student_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_child_comments');
    }
};
