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
use App\Http\Controllers\Student\PostCommentController;
use App\Http\Controllers\Student\PostChildCommentController;

// =======================
// STUDENT API ROUTES
// =======================

Route::prefix('student')->group(function () {

    // ----------- AUTH -----------
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        // ----------- PROFILE -----------
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::delete('/photo', [AuthController::class, 'deletePhoto']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // ----------- DASHBOARD -----------
        Route::get('/dashboard', [DashboardController::class, 'index']);

        // ----------- CLASSROOMS -----------
        Route::get('/classrooms', [ClassroomController::class, 'index']);
        Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);

        // ----------- POSTS & TASKS -----------
        Route::get('/materials', [PostController::class, 'materials']);
        Route::get('/assignments', [PostController::class, 'assignments']);
        Route::get('/assignments/{id}', [PostController::class, 'show']);
        Route::get('/posts/{id}', [PostController::class, 'show']);

        // ----------- STUDENT TASK SUBMISSION -----------
        Route::get('/my-tasks', [TaskController::class, 'index']);
        Route::post('/submit-task', [TaskController::class, 'store']);

        // ----------- EXERCISES -----------
        Route::get('/exercise-lessons', [ExerciseController::class, 'index']);
        Route::get('/lesson/{lessonId}/exercises', [ExerciseController::class, 'exercisesByLesson']);
        Route::get('/exercises', [ExerciseController::class, 'index_old']);
        Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
        Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submit']);
        Route::get('/exercises/{id}/result', [ExerciseController::class, 'result']);

        // ----------- MEETINGS (ONLINE CLASS) -----------
        Route::get('/meetings', [MeetingController::class, 'index']);

        // JOIN meeting (insert participant siswa)
        Route::post('/meetings/{id}/join', [MeetingController::class, 'join']);

        // LEAVE meeting (update left_at)
        Route::post('/meetings/{id}/leave', [MeetingController::class, 'leave']);

        // ----------- DAILY REPORTS -----------
        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports', [ReportController::class, 'store']);
        Route::get('/reports/{id}', [ReportController::class, 'show']);
        Route::get('/reports/check/today', [ReportController::class, 'checkToday']);

        // ----------- GRADES -----------
        Route::get('/grades/tasks', [GradeController::class, 'taskGrades']);
        Route::get('/grades/exercises', [GradeController::class, 'exerciseGrades']);
        Route::get('/grades/summary', [GradeController::class, 'summary']);

        // ----------- COMMENTS -----------

        // List + Create Comment
        Route::get('/posts/{post}/comments', [\App\Http\Controllers\Student\PostCommentController::class, 'index']);
        Route::post('/posts/{post}/comments', [\App\Http\Controllers\Student\PostCommentController::class, 'store']);

        // Delete Comment
        Route::delete('/comments/{comment}', [\App\Http\Controllers\Student\PostCommentController::class, 'destroy']);

        // Reply Comment
        Route::post('/comments/{comment}/reply', [\App\Http\Controllers\Student\PostChildCommentController::class, 'store']);
        Route::delete('/replies/{reply}', [\App\Http\Controllers\Student\PostChildCommentController::class, 'destroy']);

        // Edit Comment
        Route::put('/comments/{comment}', [PostCommentController::class, 'update']);
        Route::put('/replies/{reply}', [PostChildCommentController::class, 'update']);

    });
});
