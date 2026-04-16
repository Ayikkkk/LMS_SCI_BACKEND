<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('serials', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->unsignedInteger('product_id');
            $table->string('serial', 50);
            $table->string('paket', 1);
            $table->string('active', 3);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('serials');
    }
};
