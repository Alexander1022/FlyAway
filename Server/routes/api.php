<?php

use App\Http\Controllers\HabitatController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SpecieController;
use App\Http\Controllers\SpecieTypeController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\UserAchievementController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\RoleController;

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::resource('locations', LocationController::class);
    Route::get('user', [UserController::class, 'getUser']);

    Route::get('species/dropdown', [SpecieController::class, 'dropdown']);
    Route::resource('species', SpecieController::class)->only(['index', 'show']);

    Route::get('specie-types/dropdown', [SpecieTypeController::class, 'dropdown']);
    Route::resource('specie-types', SpecieTypeController::class)->only(['index', 'show']);

    Route::get('habitats/dropdown', [HabitatController::class, 'dropdown']);
    Route::resource('habitats', HabitatController::class)->only(['index', 'show']);

    Route::resource('achievements', AchievementController::class)->only(['index', 'show']);
    Route::get('/user/my-locations', [LocationController::class, 'getUserLocations']);
    Route::post('/assign-achievement-points', [UserAchievementController::class, 'assignPoints']);

    Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderboard']);

    Route::resource('roles', RoleController::class)->only(['index', 'show']);

    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('species', SpecieController::class)->only(['store', 'update', 'destroy']);
        Route::resource('specie-types', SpecieTypeController::class)->only(['store', 'update', 'destroy']);
        Route::resource('habitats', HabitatController::class)->only(['store', 'update', 'destroy']);
        Route::resource('achievements', AchievementController::class)->only(['update', 'store', 'destroy']);
        Route::resource('roles', RoleController::class)->only(['update', 'destroy']);
    });
});