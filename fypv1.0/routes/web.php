<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LibraryController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\HobbyController;
use App\Http\Controllers\MilestoneController;

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

    // Goal Routes
    Route::middleware(['auth'])->group(function () {
        // Basic CRUD routes for goals
        Route::resource('goals', GoalController::class);
        
        // Milestone routes (nested under goals)
        Route::post('goals/{goal}/milestones', [MilestoneController::class, 'store'])
            ->name('goals.milestones.store');
        
        Route::put('goals/{goal}/milestones/{milestone}', [MilestoneController::class, 'update'])
            ->name('goals.milestones.update');
        
        Route::delete('goals/{goal}/milestones/{milestone}', [MilestoneController::class, 'destroy'])
            ->name('goals.milestones.destroy');
        
        // Toggle milestone completion status
        Route::post('goals/{goal}/milestones/{milestone}/toggle', 
            [MilestoneController::class, 'toggle'])
            ->name('goals.milestones.toggle')
            ->middleware('auth');
    });

    // Profile
    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'profile')->name('profile');
        Route::post('/profile', 'update')->name('profile.update');
    });

    // Library
    Route::controller(LibraryController::class)->middleware(['auth'])->group(function () {
        Route::get('/library', 'index')->name('library');
        Route::post('/library', 'store')->name('library.store');
        Route::post('/library/{item}/react', 'react')->name('library.react');
        Route::post('/library/{item}/comment', 'addComment')->name('library.comment');
        Route::post('/library/{item}/rate', 'rate')->name('library.rate');
        Route::post('/library/{item}/save', 'toggleSave')->name('library.save');
        Route::get('/library/{item}/comments', 'getComments')->name('library.comments');
        Route::get('/library/{item}/download', 'download')->name('library.download');
        Route::get('/library/my-resources', 'getMyResources')->name('library.my-resources');
        
        // Add these new routes for resource management
        Route::put('/library/{item}/update', 'updateResource')->name('library.update');
        Route::delete('/library/{item}/delete', 'deleteResource')->name('library.delete');
    });

    // Community Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
        Route::post('/community', [CommunityController::class, 'store'])->name('community.store');
        Route::get('/community/my-posts', [CommunityController::class, 'getMyPosts'])->name('community.my-posts');
        Route::get('/community/{community}', [CommunityController::class, 'show'])->name('community.show');
        Route::delete('/community/{community}', [CommunityController::class, 'destroy'])->name('community.destroy');
        Route::post('/community/{community}/comment', [CommunityController::class, 'addComment'])->name('community.comment');
        Route::get('/community/{community}/comments', [CommunityController::class, 'getComments'])->name('community.comments');
        Route::post('/community/{community}/react', [CommunityController::class, 'react'])->name('community.react');
        Route::post('/community/{community}/save', [CommunityController::class, 'toggleSave'])->name('community.save');
        Route::put('/community/{community}/update', [CommunityController::class, 'update'])
            ->name('community.update')
            ->where('community', '[0-9]+');
    });

    // Recommendation routes
    Route::get('/recommendation', [App\Http\Controllers\RecommendationController::class, 'index'])->name('recommendation');
    Route::post('/recommendation/get', [App\Http\Controllers\RecommendationController::class, 'getRecommendations'])->name('recommendation.get');
    Route::delete('/recommendations/{recommendation}', [RecommendationController::class, 'destroy'])->name('recommendations.destroy');

    // Admin Routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/system', [SystemController::class, 'index'])->name('system');
        Route::post('/system/users', [SystemController::class, 'addUser'])->name('system.addUser');
        Route::delete('/system/users/{id}', [SystemController::class, 'deleteUser'])->name('system.deleteUser');
        Route::delete('/system/resources/{resource}', [SystemController::class, 'deleteResource'])->name('system.deleteResource');
        Route::delete('/system/comments/{comment}', [SystemController::class, 'deleteComment'])->name('system.deleteComment');
        Route::delete('/system/community-posts/{post}', [SystemController::class, 'deleteCommunityPost'])->name('system.deleteCommunityPost');
        Route::delete('/system/community-comments/{comment}', [SystemController::class, 'deleteCommunityComment'])->name('system.deleteCommunityComment');
    });

    // Hobby Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/hobbies', [HobbyController::class, 'index'])->name('hobbies.index');
        Route::post('/hobbies', [HobbyController::class, 'store'])->name('hobbies.store');
        Route::get('/hobbies/create', [HobbyController::class, 'create'])->name('hobbies.create');
        Route::get('/hobbies/{hobby}', [HobbyController::class, 'show'])->name('hobbies.show');
        Route::put('/hobbies/{hobby}/update', [HobbyController::class, 'update'])->name('hobbies.update');
        Route::delete('/hobbies/{hobby}', [HobbyController::class, 'destroy'])->name('hobbies.destroy');
    });

    Route::resource('hobbies', HobbyController::class)->middleware('auth');
});

// Add these routes for fetching goals and milestones
Route::get('/api/hobbies/{hobby}/goals', function ($hobby) {
    $hobby = App\Models\Hobby::findOrFail($hobby);
    return response()->json($hobby->goals->map(function ($goal) {
        return [
            'id' => $goal->id,
            'goal' => $goal->goal, // Using 'goal' property instead of 'title'
            'status' => $goal->status,
            'progress' => $goal->progress
        ];
    }));
});

Route::get('/api/goals/{goal}/milestones', function ($goal) {
    $goal = App\Models\Goal::with('milestones')->findOrFail($goal);
    return response()->json([
        'id' => $goal->id,
        'goal' => $goal->goal, // Using 'goal' property instead of 'title'
        'milestones' => $goal->milestones->map(function ($milestone) {
            return [
                'id' => $milestone->id,
                'description' => $milestone->description,
                'due_date' => $milestone->due_date,
                'completed' => $milestone->completed
            ];
        })
    ]);
});
























