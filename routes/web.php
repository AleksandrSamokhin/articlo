<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\Dashboard;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user:username}', [UserController::class, 'show'])->name('users.show');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');

    Route::post('/users/{user}/follow', [FollowController::class, 'store'])->name('users.follow');
    Route::delete('/users/{user}/follow', [FollowController::class, 'destroy'])->name('users.unfollow');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified', IsAdminMiddleware::class])->name('dashboard');

    Route::prefix('dashboard')
        ->name('dashboard.')
        ->group(function () {
            Route::resource('posts', Dashboard\PostController::class);
        });

});

// Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('search/{term}', [PostController::class, 'search'])->name('posts.search');

Route::get('categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::post('upload', [UploadController::class, 'store']);
Route::delete('upload', [UploadController::class, 'destroy']);

require __DIR__.'/auth.php';
