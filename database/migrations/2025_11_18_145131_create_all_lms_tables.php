<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // USERS (Guru)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->string('email', 100)->nullable();
            $table->tinyInteger('role')->default(0); // 1=guru, 2=admin
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('img', 100)->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamps();
        });

        // MAPELS
        Schema::create('mapels', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        // LESSONS
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('grade', 10);
            $table->tinyInteger('semester');
            $table->tinyInteger('category')->default(1);
            $table->timestamps();
        });

        // PRODUCTS
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->string('name', 50);
            $table->string('grade', 50)->nullable();
            $table->string('grade_category', 100);
            $table->string('semester', 50)->nullable();
            $table->timestamps();
        });

        // SERIALS
        Schema::create('serials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->string('serial', 50);
            $table->string('paket', 1);
            $table->string('active', 3);
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });

        // CLASSROOMS
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('grade', 10);
            $table->string('code', 24);
            $table->timestamps();
        });
        
        // COMPETENCES
        Schema::create('competences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->string('point', 10);
            $table->text('description');
            $table->timestamps();
        });

        // EXERCISE TYPES
        Schema::create('exercise_types', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10);
            $table->string('name', 50);
            $table->timestamps();
        });

        // EXERCISE MODELS
        Schema::create('exercise_models', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->timestamps();
        });

        // EXERCISES
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('lessons')->cascadeOnDelete();
            $table->foreignId('serial_id')->nullable()->constrained('serials')->nullOnDelete();
            $table->foreignId('exercise_type_id')->constrained('exercise_types')->cascadeOnDelete();
            $table->string('title', 200)->nullable();
            $table->boolean('is_admin')->default(1);
            $table->timestamps();
        });

        // EXERCISE ITEMS
        Schema::create('exercise_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();            $table->foreignId('competence_id')->nullable()->constrained('competences')->nullOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->foreignId('exercise_type_id')->constrained('exercise_types')->cascadeOnDelete();
            $table->foreignId('exercise_model_id')->constrained('exercise_models')->cascadeOnDelete();
            $table->tinyInteger('exercise_choice');
            $table->integer('exercise_number');
            $table->text('question');
            $table->text('selection')->nullable();
            $table->text('answer')->nullable();
            $table->boolean('is_user')->default(false);
            $table->timestamps();
        });

        // STUDENTS
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('username', 100)->unique();
            $table->string('password', 150);
            $table->string('password_text', 100);
            $table->string('nis', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamps();
        });

        // EXERCISE POINTS
        Schema::create('exercise_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('answer');
            $table->text('competence_point')->nullable();
            $table->string('exercise_point', 3)->nullable();
            $table->timestamps();
        });

        // POSTS
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mapel_id')->constrained('mapels')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('slug', 200);
            $table->text('link')->nullable();
            $table->text('attachment')->nullable();
            $table->text('embed')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_task')->default(false);
            $table->timestamps();
        });

        // POST COMMENTS
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->text('message');
            $table->string('code', 50);
            $table->boolean('is_user')->default(false);
            $table->timestamps();
        });

        // POST CHILD COMMENTS
        Schema::create('post_child_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_comment_id')->constrained('post_comments')->cascadeOnDelete();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->text('message');
            $table->boolean('is_user')->default(false);
            $table->timestamps();
        });

        // TASKS
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('description');
            $table->text('attachment')->nullable();
            $table->string('point', 3)->nullable();
            $table->timestamps();
        });

        // ONLINE MEETINGS
        Schema::create('online_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('meeting_code', 50);
            $table->text('meeting_link');
            $table->string('platform', 50)->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['upcoming', 'live', 'ended', 'cancelled'])->default('upcoming');
            $table->timestamps();
        });

        // REPORTS
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained('serials')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->text('report');
            $table->string('img', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('online_meetings');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('post_child_comments');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('students');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('serials');
        Schema::dropIfExists('products');
        Schema::dropIfExists('exercise_points');
        Schema::dropIfExists('exercise_items');
        Schema::dropIfExists('exercises');
        Schema::dropIfExists('exercise_models');
        Schema::dropIfExists('exercise_types');
        Schema::dropIfExists('competences');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('mapels');
        Schema::dropIfExists('users');
    }
};
