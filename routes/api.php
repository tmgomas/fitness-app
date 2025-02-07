<?php

use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserFoodLogController;
use App\Http\Controllers\API\UserHealthDataController;
use App\Http\Controllers\Api\UserMealLogController;
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

    Route::get('/current-user', [UserController::class, 'getCurrentUser']);
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


    Route::get('meal-logs', [UserMealLogController::class, 'index']);

    // Create new meal log
    Route::post('meal-logs', [UserMealLogController::class, 'store']);

    // Get single meal log by ID
    Route::get('meal-logs/{log_id}', [UserMealLogController::class, 'show']);

    // Update meal log
    Route::put('meal-logs/{log_id}', [UserMealLogController::class, 'update']);

    // Delete meal log
    Route::delete('meal-logs/{log_id}', [UserMealLogController::class, 'destroy']);

    Route::get('food-logs', [UserFoodLogController::class, 'index']);
    Route::post('food-logs', [UserFoodLogController::class, 'store']);
    Route::get('food-logs/{log_id}', [UserFoodLogController::class, 'show']);
    Route::put('food-logs/{log_id}', [UserFoodLogController::class, 'update']);
    Route::delete('food-logs/{log_id}', [UserFoodLogController::class, 'destroy']);

    Route::get('meals/search', [MealController::class, 'search']); // Search route MUST come before {meal} routes
    Route::get('meals', [MealController::class, 'index']);
    Route::post('meals', [MealController::class, 'store']);
    Route::get('meals/{meal}', [MealController::class, 'show']);
    Route::post('meals/{meal}', [MealController::class, 'update']);
    Route::delete('meals/{meal}', [MealController::class, 'destroy']);

    Route::get('food-items/search', [FoodItemController::class, 'search']); // Move this BEFORE the {foodItem} route
    Route::get('food-items', [FoodItemController::class, 'index']);
    Route::post('food-items', [FoodItemController::class, 'store']);
    Route::get('food-items/{foodItem}', [FoodItemController::class, 'show']);
    Route::put('food-items/{foodItem}', [FoodItemController::class, 'update']);
    Route::delete('food-items/{foodItem}', [FoodItemController::class, 'destroy']);
});
