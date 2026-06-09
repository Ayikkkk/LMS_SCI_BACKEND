<?php

use Illuminate\Support\Facades\Route;

// STUDENT CONTROLLERS
use App\Http\Controllers\Student\AuthController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\ClassroomController;
use App\Http\Controllers\Student\PostController;
use App\Http\Controllers\Student\TaskController;
use App\Http\Controllers\Student\ExerciseController;
use App\Http\Controllers\Student\ReportController;
use App\Http\Controllers\Student\MeetingController;
use App\Http\Controllers\Student\GradeController;
use App\Http\Controllers\Student\PostCommentController;
use App\Http\Controllers\Student\PostChildCommentController;

// =======================
// STUDENT API ROUTES
// =======================
Route::prefix('student')->group(function () {

    /**
     * ==========================
     * AUTH (PUBLIC)
     * ==========================
     */
    // Rate limit: max 5 attempts per minute per IP
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    /**
     * ==========================
     * AUTHENTICATED STUDENT
     * ==========================
     */
    Route::middleware('auth:sanctum')->group(function () {

        /**
         * --------------------------
         * AUTH & PROFILE
         * --------------------------
         */
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::post('/profile/update', [AuthController::class, 'updateProfile']); // multipart upload
        Route::delete('/photo', [AuthController::class, 'deletePhoto']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::post('/logout', [AuthController::class, 'logout']);

        /**
         * --------------------------
         * DASHBOARD
         * --------------------------
         */
        Route::get('/dashboard', [DashboardController::class, 'index']);

        /**
         * --------------------------
         * CLASSROOM
         * --------------------------
         */
        Route::get('/classrooms', [ClassroomController::class, 'index']);
        Route::get('/classrooms/{id}', [ClassroomController::class, 'show']);

        /**
         * --------------------------
         * MATERIALS & ASSIGNMENTS
         * --------------------------
         */
        Route::get('/materials', [PostController::class, 'materials']);
        Route::get('/assignments', [PostController::class, 'assignments']);
        Route::get('/assignments/{id}', [PostController::class, 'show']);
        Route::get('/posts/{id}', [PostController::class, 'show']);
        Route::get(
            '/posts/{id}/download',
            [PostController::class, 'downloadAttachment']
        );


        /**
         * --------------------------
         * TASK SUBMISSION
         * --------------------------
         */
        Route::get('/my-tasks', [TaskController::class, 'index']);
        Route::post('/submit-task', [TaskController::class, 'store']);
        Route::post('/submit-task/{postId}/update', [TaskController::class, 'update']);
        Route::get('/tasks/{postId}/download', [TaskController::class, 'download']);

        /**
         * --------------------------
         * EXERCISES
         * --------------------------
         */
        Route::get('/exercise-lessons', [ExerciseController::class, 'index']);
        Route::get('/lesson/{lessonId}/exercises', [ExerciseController::class, 'exercisesByLesson']);
        Route::get('/exercises', [ExerciseController::class, 'index_old']);
        Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
        Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submit']);
        Route::get('/exercises/{id}/result', [ExerciseController::class, 'result']);
        Route::post('/quiz/log', [ExerciseController::class, 'logActivity']);

        /**
         * --------------------------
         * ONLINE MEETINGS
         * --------------------------
         */
        Route::get('/meetings', [MeetingController::class, 'index']);
        Route::post('/meetings/{id}/join', [MeetingController::class, 'join']);
        Route::post('/meetings/{id}/leave', [MeetingController::class, 'leave']);

        /**
         * --------------------------
         * DAILY REPORTS
         * --------------------------
         */
        Route::get('/reports', [ReportController::class, 'index']);
        Route::post('/reports', [ReportController::class, 'store']);
        Route::get('/reports/{id}', [ReportController::class, 'show']);
        Route::get('/reports/check/today', [ReportController::class, 'checkToday']);

        /**
         * --------------------------
         * GRADES
         * --------------------------
         */

        // Dashboard / legacy
        Route::get('/grades/tasks', [GradeController::class, 'taskGrades']);
        Route::get('/grades/exercises', [GradeController::class, 'exerciseGrades']);
        Route::get('/grades/summary', [GradeController::class, 'summary']);

        // Rekap nilai per mapel (DINAMIS)
        Route::get('/grades/rekap-mapel', [GradeController::class, 'recapPerMapel']);

        // Download PDF rekap nilai
        Route::get('/grades/rekap-mapel/pdf', [GradeController::class, 'downloadRecapPdf']);

        /**
         * --------------------------
         * COMMENTS
         * --------------------------
         */
        Route::get('/posts/{post}/comments', [PostCommentController::class, 'index']);
        Route::post('/posts/{post}/comments', [PostCommentController::class, 'store']);
        Route::put('/comments/{comment}', [PostCommentController::class, 'update']);
        Route::delete('/comments/{comment}', [PostCommentController::class, 'destroy']);
        Route::post('/comments/{comment}/reply', [PostChildCommentController::class, 'store']);
        Route::put('/replies/{reply}', [PostChildCommentController::class, 'update']);
        Route::delete('/replies/{reply}', [PostChildCommentController::class, 'destroy']);
    });
});

// =======================
// PUBLIC IMAGE PROXY
// Download gambar dari domain eksternal (backend guru) dan teruskan ke Flutter
// Menghindari SSL/CORS issue saat HP load gambar langsung dari domain guru
// =======================
Route::get('/proxy-image', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');

    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        abort(400, 'Invalid URL');
    }

    // Whitelist domain yang diizinkan
    $allowedDomains = [
        'tak-scimediaonline.my.id',
        '151.243.222.93',
        '127.0.0.1',
    ];

    $host = parse_url($url, PHP_URL_HOST);
    $allowed = collect($allowedDomains)->contains(fn($d) => str_contains($host, $d));

    if (!$allowed) {
        abort(403, 'Domain not allowed');
    }

    try {
        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
            'http' => [
                'timeout' => 10,
            ],
        ]);

        $imageData = file_get_contents($url, false, $context);

        if ($imageData === false) {
            abort(404, 'Image not found');
        }

        $mimeType = 'image/jpeg'; // default
        foreach ($http_response_header as $header) {
            if (stripos($header, 'Content-Type:') !== false) {
                $mimeType = trim(explode(':', $header, 2)[1]);
                break;
            }
        }

        return response($imageData, 200, [
            'Content-Type'                => $mimeType,
            'Cache-Control'               => 'public, max-age=86400',
            'Access-Control-Allow-Origin' => '*',
        ]);
    } catch (\Exception $e) {
        abort(500, 'Failed to fetch image');
    }
});
Route::get('/files/{path}', function (string $path) {
    $fullPath = storage_path('app/public/' . $path);

    if (!file_exists($fullPath)) {
        // Fallback: coba tanpa prefix (jika path sudah mengandung subfolder)
        abort(404, 'File tidak ditemukan');
    }

    $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type'                => $mimeType,
        'Cache-Control'               => 'public, max-age=86400',
        'Access-Control-Allow-Origin' => '*',
    ]);
})->where('path', '.*');
