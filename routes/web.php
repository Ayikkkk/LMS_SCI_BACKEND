<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Student\PostController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/cek', fn() => 'web aktif');



Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

