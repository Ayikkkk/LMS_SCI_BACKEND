<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Teacher\OnlineMeetingController;
use App\Http\Controllers\Student\PostController;

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
    });

/*
|--------------------------------------------------------------------------
| Routes Post Student (Testing Only)
|--------------------------------------------------------------------------
*/
Route::get('/posts/create', [PostController::class, 'create'])
    ->name('posts.create');

Route::post('/posts', [PostController::class, 'store'])
    ->name('posts.store');

/*
|--------------------------------------------------------------------------
| Debug Route (sementara)
|--------------------------------------------------------------------------
*/
Route::get('/debug-route', function () {
    Artisan::call('route:list');
    return nl2br(Artisan::output());
});
