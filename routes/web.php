<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
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
// Akses: https://lmsscibackend-production.up.railway.app/maintenance/clear-missing-photos
Route::get('/maintenance/clear-missing-photos', function () {
    // Proteksi sederhana dengan secret key
    $secret = request()->query('key');
    if ($secret !== env('MAINTENANCE_KEY', 'lms-maintenance-2026')) {
        abort(403, 'Forbidden');
    }

    $students = \DB::table('students')->whereNotNull('photo')->get(['id', 'photo']);
    $cleared = 0;

    foreach ($students as $student) {
        $path = $student->photo;

        // Jika sudah full URL, ambil path relatifnya
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Ambil bagian setelah /storage/
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = ltrim(str_replace('/storage/', '', $parsed), '/');
        }

        // Cek apakah file fisik ada di storage
        if (!\Storage::disk('public')->exists($path)) {
            \DB::table('students')->where('id', $student->id)->update(['photo' => null]);
            $cleared++;
        }
    }

    return response()->json([
        'success' => true,
        'message' => "Selesai. $cleared foto dihapus dari database (file tidak ditemukan di server).",
        'total_checked' => $students->count(),
        'total_cleared' => $cleared,
    ]);
});

// Cek status storage
// Akses: https://lmsscibackend-production.up.railway.app/maintenance/storage-status
Route::get('/maintenance/storage-status', function () {
    $secret = request()->query('key');
    if ($secret !== env('MAINTENANCE_KEY', 'lms-maintenance-2026')) {
        abort(403, 'Forbidden');
    }

    $linkExists = file_exists(public_path('storage'));
    $storageWritable = is_writable(storage_path('app/public'));
    $students = \DB::table('students')->whereNotNull('photo')->get(['id', 'photo']);

    $fileStatus = $students->map(function ($s) {
        $path = $s->photo;
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path, PHP_URL_PATH);
            $path = ltrim(str_replace('/storage/', '', $parsed), '/');
        }
        return [
            'id' => $s->id,
            'photo_db' => $s->photo,
            'path_checked' => $path,
            'file_exists' => \Storage::disk('public')->exists($path),
        ];
    });

    return response()->json([
        'storage_link_exists' => $linkExists,
        'storage_writable' => $storageWritable,
        'students_with_photo' => $students->count(),
        'files' => $fileStatus,
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
