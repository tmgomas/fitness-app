<?php

use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\HealthDataController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\NutritionTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserExerciseLogController;
use App\Http\Controllers\Api\UserFoodLogController;

use App\Http\Controllers\Api\UserMealLogController;
use App\Http\Controllers\API\UserMeasurementController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\FoodNutritionController;
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

    Route::apiResource('health-data', HealthDataController::class);
    Route::apiResource('preferences', UserPreferenceController::class);
    Route::apiResource('measurements', UserMeasurementController::class);
    Route::apiResource('nutrition-types', NutritionTypeController::class);
    // ___________________________________
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/current-user', [UserController::class, 'getCurrentUser']);


    Route::apiResource('food-items', FoodItemController::class);
    // Additional custom routes
    Route::get('/food-items/search', [FoodItemController::class, 'search'])
        ->name('api.food-items.search');

    Route::apiResource('food-nutrition', FoodNutritionController::class);
    Route::get('food-nutrition/search', [FoodNutritionController::class, 'search'])
        ->name('api.food-nutrition.search');

    // // Health Data Routes
    // Route::get('/health-data', [UserHealthDataController::class, 'index']);
    // Route::post('/health-data', [UserHealthDataController::class, 'store']);
    // Route::get('/health-data/{id}', [UserHealthDataController::class, 'show']);
    // Route::put('/health-data/{id}', [UserHealthDataController::class, 'update']);
    // Route::delete('/health-data/{id}', [UserHealthDataController::class, 'destroy']);

    // Preferences Routes
    // Route::get('/preferences', [UserPreferenceController::class, 'index']);
    // Route::post('/preferences', [UserPreferenceController::class, 'store']);
    // Route::get('/preferences/{id}', [UserPreferenceController::class, 'show']);
    // Route::put('/preferences/{id}', [UserPreferenceController::class, 'update']);
    // Route::delete('/preferences/{id}', [UserPreferenceController::class, 'destroy']);

    // // Measurements Routes
    // Route::get('/measurements', [UserMeasurementController::class, 'index']);
    // Route::post('/measurements', [UserMeasurementController::class, 'store']);
    // Route::get('/measurements/{id}', [UserMeasurementController::class, 'show']);
    // Route::put('/measurements/{id}', [UserMeasurementController::class, 'update']);
    // Route::delete('/measurements/{id}', [UserMeasurementController::class, 'destroy']);


    Route::get('meal_log', [UserMealLogController::class, 'index']);

    // Create new meal log
    Route::post('meal_log', [UserMealLogController::class, 'store']);

    // Get single meal log by ID
    Route::get('meal_log/{log_id}', [UserMealLogController::class, 'show']);

    // Update meal log
    Route::put('meal_log/{log_id}', [UserMealLogController::class, 'update']);

    // Delete meal log
    Route::delete('meal_log/{log_id}', [UserMealLogController::class, 'destroy']);

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

    // Route::get('food-items/search', [FoodItemController::class, 'search']); // Move this BEFORE the {foodItem} route
    // Route::get('food-items', [FoodItemController::class, 'index']);
    // Route::post('food-items', [FoodItemController::class, 'store']);
    // Route::get('food-items/{foodItem}', [FoodItemController::class, 'show']);
    // Route::put('food-items/{foodItem}', [FoodItemController::class, 'update']);
    // Route::delete('food-items/{foodItem}', [FoodItemController::class, 'destroy']);


    // Route::get('/nutrition-types', [NutritionTypeController::class, 'index']);
    // Route::post('/nutrition-types', [NutritionTypeController::class, 'store']);
    // Route::get('/nutrition-types{nutritionType}', [NutritionTypeController::class, 'show']);
    // Route::put('/nutrition-types{nutritionType}', [NutritionTypeController::class, 'update']);
    // Route::delete('/{nutritionType}', [NutritionTypeController::class, 'destroy']);

    Route::get('/exercise-logs', [UserExerciseLogController::class, 'index']);
    Route::post('/exercise-logs', [UserExerciseLogController::class, 'store']);
    Route::get('/{log_id}', [UserExerciseLogController::class, 'show']);
    Route::put('/{log_id}', [UserExerciseLogController::class, 'update']);
    Route::delete('/{log_id}', [UserExerciseLogController::class, 'destroy']);
    Route::get('/stats/summary', [UserExerciseLogController::class, 'getStats']);

    Route::get('/exercises', [ExerciseController::class, 'index']);
    Route::post('/exercises', [ExerciseController::class, 'store']);
    Route::get('/exercises/search', [ExerciseController::class, 'search']);
    Route::get('/category/{categoryId}', [ExerciseController::class, 'getByCategory']);
    Route::get('/{exercise}', [ExerciseController::class, 'show']);
    Route::put('/{exercise}', [ExerciseController::class, 'update']);
    Route::delete('/{exercise}', [ExerciseController::class, 'destroy']);
    Route::put('/{exercise}/toggle-status', [ExerciseController::class, 'toggleStatus']);
});
