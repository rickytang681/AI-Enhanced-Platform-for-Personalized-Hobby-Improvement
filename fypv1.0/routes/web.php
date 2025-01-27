<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;


/*
|--------------------------------------------------------------------------|
| Web Routes                                                               |
|--------------------------------------------------------------------------|
| Here is where you can register web routes for your application. These    |
| routes are loaded by the RouteServiceProvider within a group which       |
| contains the "web" middleware group. Now create something great!         |
|--------------------------------------------------------------------------|
*/

// Public routes
Route::get('/', [MainController::class, 'index']);
Route::get('/goal', [MainController::class, 'goal']);
Route::get('/milestone', [MainController::class, 'milestone']);
Route::get('/progressTracking', [MainController::class, 'progressTracking']);
Route::get('/recommendation', [MainController::class, 'recommendation']);
Route::get('/library', [MainController::class, 'library']);
Route::get('/community', [MainController::class, 'community']);
Route::get('/home', [MainController::class, 'dashboard']);

// Authentication routes
Auth::routes();

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/system', [MainController::class, 'system'])->name('system');
});

// Profile routes

Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profile.edit');
Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
