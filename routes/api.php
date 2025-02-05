<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\API\UserHealthDataController;
use App\Http\Controllers\API\UserMeasurementController;
use App\Http\Controllers\API\UserPreferenceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [UserController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    // Health Data Routes
    Route::get('/health-data', [UserHealthDataController::class, 'index']);
    Route::post('/health-data', [UserHealthDataController::class, 'store']);
    Route::get('/health-data/{id}', [UserHealthDataController::class, 'show']);
    Route::put('/health-data/{id}', [UserHealthDataController::class, 'update']);
    Route::delete('/health-data/{id}', [UserHealthDataController::class, 'destroy']);

    // Preferences Routes
    Route::get('/preferences', [UserPreferenceController::class, 'index']);
    Route::post('/preferences', [UserPreferenceController::class, 'store']);
    Route::get('/preferences/{id}', [UserPreferenceController::class, 'show']);
    Route::put('/preferences/{id}', [UserPreferenceController::class, 'update']);
    Route::delete('/preferences/{id}', [UserPreferenceController::class, 'destroy']);

    // Measurements Routes
    Route::get('/measurements', [UserMeasurementController::class, 'index']);
    Route::post('/measurements', [UserMeasurementController::class, 'store']);
    Route::get('/measurements/{id}', [UserMeasurementController::class, 'show']);
    Route::put('/measurements/{id}', [UserMeasurementController::class, 'update']);
    Route::delete('/measurements/{id}', [UserMeasurementController::class, 'destroy']);
});
