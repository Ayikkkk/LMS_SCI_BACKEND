<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('serial_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('mapel_id');
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('slug', 200);
            $table->text('link')->nullable();
            $table->text('attachment')->nullable();
            $table->text('embed')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->string('category', 255)->nullable();
            $table->boolean('is_task')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('serial_id')->references('id')->on('serials')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('mapel_id')->references('id')->on('mapels')->cascadeOnDelete();
            $table->index(['serial_id', 'is_task']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
