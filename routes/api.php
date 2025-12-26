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
        // Profile
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);       // update profil (multipart/form-data)
        Route::delete('/photo', [AuthController::class, 'deletePhoto']);       // hapus foto profil
        Route::post('/change-password', [AuthController::class, 'changePassword']); // ganti password

        // Logout
        Route::post('/logout', [AuthController::class, 'logout']);

        // Dashboard
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
        // NEW: daftar lessons (mapel) yang punya exercises untuk siswa (untuk tampilan card + tipe)
        Route::get('/exercise-lessons', [ExerciseController::class, 'index']);

        // NEW: daftar exercises untuk lesson tertentu, optional query ?type_id=...
        Route::get('/lesson/{lessonId}/exercises', [ExerciseController::class, 'exercisesByLesson']);

        // Existing endpoints (tetap ada untuk backward compatibility)
        Route::get('/exercises', [ExerciseController::class, 'index_old'] ?? [ExerciseController::class, 'index']);
        Route::get('/exercises/{id}', [ExerciseController::class, 'show']); // Detail quiz
        Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submit']); // Kirim jawaban
        Route::get('/exercises/{id}/result', [ExerciseController::class, 'result']);   // Lihat hasil

        // ----------- MEETINGS (Kelas Online) -----------
        // List meeting berdasarkan classroom siswa
        Route::get('/meetings', [MeetingController::class, 'index']);
        // Detail meeting
        Route::get('/meetings/{id}', [MeetingController::class, 'show']);
        // Join meeting (absensi + generate Jitsi URL)
        Route::post('/meetings/{id}/join', [MeetingController::class, 'join']);
        // Leave meeting (update waktu keluar)
        Route::post('/meetings/{id}/leave', [MeetingController::class, 'leave']);


        // ----------- DAILY REPORTS -----------
        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports', [ReportController::class, 'store']);
        Route::get('/reports/{id}', [ReportController::class, 'show']);
        Route::get('/reports/check/today', [ReportController::class, 'checkToday']);

        // ----------- GRADES (Nilai) -----------
        Route::get('/grades/tasks', [GradeController::class, 'taskGrades']);
        Route::get('/grades/exercises', [GradeController::class, 'exerciseGrades']);
        Route::get('/grades/summary', [GradeController::class, 'summary']);
    });
});
