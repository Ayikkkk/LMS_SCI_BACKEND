<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\ClassroomController;
use App\Http\Controllers\Student\PostController;
use App\Http\Controllers\Student\TaskController;
use App\Http\Controllers\Student\ExerciseController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\MeetingController;
use App\Http\Controllers\Student\GradeController;
use App\Http\Controllers\Student\DashboardController;

// =======================
// STUDENT API ROUTES
// =======================

Route::prefix('student')->group(function () {

    // ----------- AUTH -----------
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // ----------- CLASSROOMS -----------
        Route::get('/classrooms', [ClassroomController::class, 'index']);
        Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);

        // ----------- POSTS & TASKS -----------
        // Rute Materi (Match: /student/materials)
        Route::get('/materials', [PostController::class, 'materials']);
        // Rute Tugas (Match: /student/assignments)
        Route::get('/assignments', [PostController::class, 'assignments']);

        // Detail tugas atau materi berdasarkan ID
        Route::get('/assignments/{id}', [PostController::class, 'show']);
        // Detail materi/tugas
        Route::get('/posts/{id}', [PostController::class, 'show']);

        // ----------- STUDENT TASK SUBMISSION -----------
        Route::get('/my-tasks', [TaskController::class, 'index']);      // Riwayat tugas siswa
        Route::post('/submit-task', [TaskController::class, 'store']);  // Kirim tugas baru

        // ----------- EXERCISES (Quiz / Latihan) -----------
        Route::get('/exercises', [ExerciseController::class, 'index']); // List quiz
        Route::get('/exercises/{id}', [ExerciseController::class, 'show']); // Detail quiz
        Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submit']); // Kirim jawaban
        Route::get('/exercises/{id}/result', [ExerciseController::class, 'result']);   // Lihat hasil

        // ----------- MEETINGS (Kelas Online) -----------
        Route::get('/meetings', [MeetingController::class, 'index']);
        Route::get('/meetings/{id}', [MeetingController::class, 'show']);

        // ----------- DAILY REPORTS -----------
        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports', [ReportController::class, 'store']);
        Route::get('/reports/{id}', [ReportController::class, 'show']);

        // ----------- GRADES (Nilai) -----------
        Route::get('/grades/tasks', [GradeController::class, 'taskGrades']);
        Route::get('/grades/exercises', [GradeController::class, 'exerciseGrades']);
        Route::get('/grades/summary', [GradeController::class, 'summary']);
    });
});

