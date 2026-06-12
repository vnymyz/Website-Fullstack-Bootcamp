<?php

use Illuminate\Support\Facades\Route;
// kita panggil controller hello world nya
use App\Http\Controllers\HelloController;
// post controller
use App\Http\Controllers\PostController;

Route::get('/', function () {
    return view('welcome');
});

// untuk menambahkan route baru hello world
// Route::get('/hello', function () {
//     return view('hello', ['name' => 'Vanya']);
// });

// route hello world sudah dipindah logic nya ke controller
Route::get('/hello', [HelloController::class, 'index']);

// rooute hello {name} untuk menampilkan nama yang berbeda atau dinamis
Route::get('/hello/{name}', [HelloController::class, 'show']);

// tambah route posts untuk menampilkan semua post
Route::get('/posts', [PostController::class, 'index']);
