<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Teacher\OnlineMeetingController;
use App\Http\Controllers\Student\PostController;
use App\Http\Controllers\Teacher\PostCommentController as TeacherPostCommentController;
use App\Http\Controllers\Teacher\PostChildCommentController as TeacherPostChildCommentController;
use App\Http\Controllers\Teacher\QuizLogController;

/*
|--------------------------------------------------------------------------
| Online Meeting Guru (Web)
|--------------------------------------------------------------------------
| Note:
| - Testing mode (tanpa auth) OK untuk sekarang
| - Jika nanti pakai auth guru, tinggal bungkus middleware
|--------------------------------------------------------------------------
*/

Route::prefix('teacher')
    ->name('teacher.')
    ->group(function () {

        // ================= ONLINE MEETINGS =================

        // List meeting guru
        Route::get('/online-meetings', [OnlineMeetingController::class, 'index'])
            ->name('meetings.index');

        // Form create meeting
        Route::get('/online-meetings/create', [OnlineMeetingController::class, 'create'])
            ->name('meetings.create');

        // Simpan meeting baru
        Route::post('/online-meetings', [OnlineMeetingController::class, 'store'])
            ->name('meetings.store');

        // Start meeting (set live + catat guru join)
        Route::post('/online-meetings/{id}/start', [OnlineMeetingController::class, 'start'])
            ->name('meetings.start');

        // End meeting (set ended + close participants)
        Route::post('/online-meetings/{id}/end', [OnlineMeetingController::class, 'end'])
            ->name('meetings.end');

        // ================= POSTS COMMENTS ==================
        // Comments list (JSON untuk testing)
        Route::get('/posts/{post}/comments', [TeacherPostCommentController::class, 'index'])
            ->name('comments.index');

        // Create comment
        Route::post('/posts/{post}/comments', [TeacherPostCommentController::class, 'store'])
            ->name('comments.store');

        // Delete comment
        Route::delete('/comments/{comment}', [TeacherPostCommentController::class, 'destroy'])
            ->name('comments.destroy');

        // Create reply
        Route::post('/comments/{comment}/reply', [TeacherPostChildCommentController::class, 'store'])
            ->name('comments.reply.store');

        // Delete reply
        Route::delete('/replies/{reply}', [TeacherPostChildCommentController::class, 'destroy'])
            ->name('comments.reply.destroy');

        Route::get('/posts/create', [PostController::class, 'create'])
            ->name('posts.create');

        Route::post('/posts', [PostController::class, 'store'])
            ->name('posts.store');

        // ================= QUIZ LOG MONITORING =================
        Route::get('/quiz-logs', [QuizLogController::class, 'index'])
            ->name('quiz-logs.index');

        Route::get('/quiz-logs/{exerciseId}', [QuizLogController::class, 'show'])
            ->name('quiz-logs.show');
    });

/*
|--------------------------------------------------------------------------
| Maintenance Routes (sementara — hapus setelah selesai dipakai)
|--------------------------------------------------------------------------
*/

// Bersihkan kolom photo siswa yang filenya tidak ada di server
// Akses: https://lmsscibackend-production.up.railway.app/maintenance/clear-missing-photos?key=lms-maintenance-2026
Route::get('/maintenance/clear-missing-photos', function () {
    if (request()->query('key') !== env('MAINTENANCE_KEY', 'lms-maintenance-2026')) {
        abort(403, 'Forbidden');
    }

    /** @var \Illuminate\Support\Collection<int, \App\Models\Student> $students */
    $students = \App\Models\Student::whereNotNull('photo')->get(['id', 'photo']);
    $cleared  = 0;

    foreach ($students as $student) {
        $raw = $student->getRawOriginal('photo') ?? '';

        // Jika full URL, ambil path relatif setelah /storage/
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            $urlPath = (string) parse_url($raw, PHP_URL_PATH);
            $raw     = ltrim(str_replace('/storage/', '', $urlPath), '/');
        }

        if (!Storage::disk('public')->exists($raw)) {
            $student->photo = null;
            $student->save();
            $cleared++;
        }
    }

    return response()->json([
        'success'       => true,
        'message'       => "Done. $cleared photo(s) cleared (file not found on server).",
        'total_checked' => $students->count(),
        'total_cleared' => $cleared,
    ]);
});

// Cek status storage
// Akses: https://lmsscibackend-production.up.railway.app/maintenance/storage-status?key=lms-maintenance-2026
Route::get('/maintenance/storage-status', function () {
    if (request()->query('key') !== env('MAINTENANCE_KEY', 'lms-maintenance-2026')) {
        abort(403, 'Forbidden');
    }

    /** @var \Illuminate\Support\Collection<int, \App\Models\Student> $students */
    $students = \App\Models\Student::whereNotNull('photo')->get(['id', 'photo']);

    $files = $students->map(function (\App\Models\Student $s) {
        $raw = $s->getRawOriginal('photo') ?? '';
        if (str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://')) {
            $urlPath = (string) parse_url($raw, PHP_URL_PATH);
            $raw     = ltrim(str_replace('/storage/', '', $urlPath), '/');
        }
        return [
            'id'           => $s->id,
            'photo_db'     => $s->getRawOriginal('photo'),
            'path_checked' => $raw,
            'file_exists'  => Storage::disk('public')->exists($raw),
        ];
    });

    return response()->json([
        'storage_link_exists'  => file_exists(public_path('storage')),
        'storage_writable'     => is_writable(storage_path('app/public')),
        'students_with_photo'  => $students->count(),
        'files'                => $files,
    ]);
});

/*
|--------------------------------------------------------------------------
| Debug Route (sementara)
|--------------------------------------------------------------------------
*/
Route::get('/debug-route', function () {
    Artisan::call('route:list');
    return nl2br(Artisan::output());
});
