<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', [MainController::class, 'index']);

Route::get('/dashboard', [MainController::class, 'dashboard']);
Route::get('/goal', [MainController::class, 'goal']);

Route::get('/milestone', [MainController::class, 'milestone']);
Route::get('/progressTracking', [MainController::class, 'progressTracking']);
Route::get('/recommendation', [MainController::class, 'recommendation']);
Route::get('/library', [MainController::class, 'library']);
Route::get('/community', [MainController::class, 'community']);

Route::get('/system', [MainController::class, 'system']);

Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
