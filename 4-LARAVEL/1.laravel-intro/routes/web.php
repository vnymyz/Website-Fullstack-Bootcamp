<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Models\Post;
use App\Models\User;

Route::get('/', function () {
    $posts = Post::latest()->take(6)->get();

    return view('home', ['posts' => $posts]);
});

Route::get('/hello', [HelloController::class, 'index']);
Route::get('/hello/{name}', [HelloController::class, 'show']);

Route::get('/dashboard', function () {
    $user = auth()->user();

    $stats = [
        'my_posts' => $user->posts()->count(),
    ];

    if ($user->role === 'admin') {
        $stats['total_posts'] = Post::count();
        $stats['total_users'] = User::count();
    }

    return view('dashboard', ['stats' => $stats]);
})->middleware(['auth', 'verified'])->name('dashboard');

// semua route yang ada di dalam group ini harus wajib login dulu
Route::middleware('auth')->group(function () {
    // buat ke halaman profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/create', [PostController::class, 'create']);
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts/{post:slug}', [PostController::class, 'show']);
    Route::get('/posts/{post}/edit', [PostController::class, 'edit']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}', [AdminUserController::class, 'updateRole'])->name('users.updateRole');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});

require __DIR__ . '/auth.php';
