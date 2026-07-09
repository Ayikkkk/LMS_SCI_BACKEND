<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
        // Route GET /exercises dihapus — index_old tidak ada di controller
        // dan tidak ada Flutter code yang memanggil endpoint ini
        Route::get('/exercises/{id}', [ExerciseController::class, 'show']);
        // Submit kuis: max 5x per menit (cegah double submit)
        Route::post('/exercises/{id}/submit', [ExerciseController::class, 'submit'])->middleware('throttle:5,1');
        Route::get('/exercises/{id}/result', [ExerciseController::class, 'result']);
        // Quiz log: max 60x per menit per user (1 log/detik sudah lebih dari cukup)
        Route::post('/quiz/log', [ExerciseController::class, 'logActivity'])->middleware('throttle:60,1');

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
// Mengambil gambar dari domain guru (whitelist only) dan meng-cache di disk lokal.
// Cache disimpan sebagai file binary — lebih efisien daripada serialize ke file cache.
// Rate limit: 120 request/menit per IP.
// =======================
Route::middleware('throttle:120,1')->get('/proxy-image', function (\Illuminate\Http\Request $request) {
    $url = $request->query('url');

    // Validasi URL ada dan formatnya valid
    if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
        abort(400, 'Invalid URL');
    }

    // Hanya izinkan http:// dan https:// — cegah file://, ftp://, dll (SSRF)
    $scheme = strtolower(parse_url($url, PHP_URL_SCHEME) ?? '');
    if (!in_array($scheme, ['http', 'https'], true)) {
        abort(400, 'Invalid URL scheme');
    }

    // Whitelist domain yang diizinkan
    $allowedDomains = [
        'tak-scimediaonline.my.id',
        'guru.tak-scimediaonline.my.id',
    ];

    $host = strtolower(parse_url($url, PHP_URL_HOST) ?? '');
    if (empty($host)) {
        abort(400, 'Invalid URL host');
    }

    $allowed = collect($allowedDomains)->contains(fn($d) => $host === $d || str_ends_with($host, '.' . $d));
    if (!$allowed) {
        abort(403, 'Domain not allowed');
    }

    // Cache: simpan sebagai file binary di storage/app/proxy-images/
    // Lebih efisien dari Cache::put(binary) yang serialize + base64 data
    $cacheFileName = 'proxy-images/' . md5($url);
    $metaKey       = 'proxy_img_meta_' . md5($url);

    // Cek file cache sudah ada
    if (Storage::exists($cacheFileName)) {
        $meta = \Illuminate\Support\Facades\Cache::get($metaKey, ['mime' => 'image/jpeg']);
        return response(Storage::get($cacheFileName), 200, [
            'Content-Type'                => $meta['mime'],
            'Cache-Control'               => 'public, max-age=86400',
            'Access-Control-Allow-Origin' => '*',
            'X-Cache'                     => 'HIT',
        ]);
    }

    // Fetch dari upstream menggunakan cURL
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 3,        // batasi redirect — cegah redirect loop
        CURLOPT_SSL_VERIFYPEER => false,    // bypass self-signed cert domain guru
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_USERAGENT      => 'LMS-Proxy/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: image/jpeg, image/png, image/gif, image/webp, image/*'],
        CURLOPT_BUFFERSIZE     => 131072,   // 128KB buffer
    ]);

    $imageData = curl_exec($ch);
    $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $mimeType  = curl_getinfo($ch, CURLINFO_CONTENT_TYPE) ?: 'image/jpeg';
    $fileSize  = curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
    $curlError = curl_error($ch);
    // curl_close deprecated di PHP 8.4+, tidak perlu dipanggil
    unset($ch);

    if ($imageData === false || !empty($curlError)) {
        Log::warning("Proxy image fetch failed: {$curlError} — URL: {$url}");
        abort(502, 'Failed to fetch image');
    }

    if ($httpCode !== 200) {
        abort($httpCode ?: 502, "Upstream returned {$httpCode}");
    }

    // Batasi ukuran — cegah gambar sangat besar menghabiskan disk
    // Gambar soal normal < 2MB
    if ($fileSize > 5 * 1024 * 1024) { // 5MB max
        abort(413, 'Image too large');
    }

    // Pastikan response adalah image — cegah download file executable
    $mimeType  = trim(explode(';', $mimeType)[0]);
    $validMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    if (!in_array($mimeType, $validMime, true)) {
        abort(415, 'Unsupported media type');
    }

    // Simpan ke disk sebagai file binary — tidak di-serialize
    Storage::put($cacheFileName, $imageData);

    // Simpan metadata (mime type) di cache — jauh lebih kecil dari binary
    \Illuminate\Support\Facades\Cache::put($metaKey, ['mime' => $mimeType], 86400);

    return response($imageData, 200, [
        'Content-Type'                => $mimeType,
        'Cache-Control'               => 'public, max-age=86400',
        'Access-Control-Allow-Origin' => '*',
        'X-Cache'                     => 'MISS',
    ]);
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
