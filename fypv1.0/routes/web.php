<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SystemController;

// Authentication Routes
Auth::routes();

// Guest Routes
Route::get('/', function () {
    return view('main');
})->middleware('guest')->name('main');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');

Route::get('/policy', function () {
    return view('policy');
})->name('policy');

Route::get('/help', function () {
    return view('help');
})->name('help');

Route::get('/aboutUs', function () {    
    return view('aboutUs');
})->name('aboutUs');    

Route::get('/contactUs', function () {
    return view('contactUs');
})->name('contactUs');   

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::redirect('/home', '/dashboard');

    // Goals
    Route::controller(GoalController::class)->group(function () {
        Route::get('/goals', 'index')->name('goals.index');
        Route::post('/goals', 'store')->name('goals.store');
        Route::post('/goals/{goal}/milestones', 'addMilestone')->name('goals.milestones.store');
        Route::post('/goals/{goal}/milestones/{milestone}/toggle', 'toggleMilestone')->name('goals.milestones.toggle');
        Route::delete('/goals/{goal}', 'destroy')->name('goals.destroy');
    });

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'profile')->name('profile');
        Route::post('/profile', 'update')->name('profile.update');
    });

    // Library
    Route::controller(LibraryController::class)->group(function () {
        Route::get('/library', 'index')->name('library');
        Route::post('/library', 'store')->name('library.store');
        Route::post('/library/{item}/react', 'react')->name('library.react');
        Route::post('/library/{item}/comment', 'addComment')->name('library.comment');
        Route::post('/library/{item}/rate', 'rate')->name('library.rate');
        Route::post('/library/{item}/save', 'toggleSave')->name('library.save');
        Route::get('/library/{item}/comments', 'getComments')->name('library.comments');
        Route::get('/library/{item}/download', 'download')->name('library.download');
    });

    // Community Routes
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
    Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
    Route::get('/community/{community}', [CommunityController::class, 'show'])->name('community.show');
    Route::post('/community/{community}/save', [CommunityController::class, 'toggleSave'])->name('community.save');
    Route::post('/community/{community}/comment', [CommunityController::class, 'addComment'])->name('community.comment');
    Route::get('/community/{community}/comments', [CommunityController::class, 'getComments'])->name('community.comments');
    Route::post('/community/{community}/react', [CommunityController::class, 'react'])->name('community.react');
    Route::delete('/community/{community}', [CommunityController::class, 'destroy'])->name('community.destroy');
    
    // Additional community routes
    Route::post('/community/{community}/react', [CommunityController::class, 'react'])->name('community.react');
    Route::post('/community/{community}/save', [CommunityController::class, 'toggleSave'])->name('community.save');

    // Recommendations
    Route::get('/recommendation', [RecommendationController::class, 'index'])->name('recommendation');

    // Admin Routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/system', [SystemController::class, 'index'])->name('system');
    });
});

