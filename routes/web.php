<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\PostController;
use App\Http\Controllers\Teacher\OnlineMeetingController;

Route::prefix('teacher')->group(function () {

    Route::get('/online-meetings', [OnlineMeetingController::class, 'index']);
    Route::get('/online-meetings/create', [OnlineMeetingController::class, 'create']);
    Route::post('/online-meetings', [OnlineMeetingController::class, 'store']);

    Route::post('/online-meetings/{id}/start', [OnlineMeetingController::class, 'start']);
    Route::post('/online-meetings/{id}/end', [OnlineMeetingController::class, 'end']);
});

Route::get('/', function () {
    return view('welcome');
});
Route::get('/cek', fn() => 'web aktif');



Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
