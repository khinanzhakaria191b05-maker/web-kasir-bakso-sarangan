<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductOptionController;
use App\Http\Controllers\Admin\UserController;

Route::redirect('/', '/login');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin & Kasir
Route::middleware(['auth', 'role:admin,kasir'])->group(function () {
    Route::get('/kasir', fn() => view('kasir.index'))->name('kasir');
});

// Khusus Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);

    Route::patch('products/{product}/toggle', [ProductController::class, 'toggle'])->name('products.toggle');
    Route::resource('products', ProductController::class)->except(['show']);

    Route::resource('options', ProductOptionController::class)
        ->except(['create', 'show', 'edit'])
        ->parameters(['options' => 'option']);

    Route::patch('users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    Route::resource('users', UserController::class)->except(['show', 'destroy']);
});
