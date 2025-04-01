<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;

Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware(['verified'])->name('dashboard');

    Route::resource('admin/posts', PostController::class);

});

Route::get('posts', [PostController::class, 'index'])->name('posts.index');
Route::get('posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('search/{term}', [PostController::class, 'search'])->name('posts.search');

require __DIR__.'/auth.php';
