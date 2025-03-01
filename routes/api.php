<?php

use App\Http\Controllers\Api\ExerciseCategoryController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\HealthDataController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\NutritionTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserExerciseLogController;
use App\Http\Controllers\Api\UserFoodLogController;
use App\Http\Controllers\Api\UserMealLogController;
use App\Http\Controllers\Api\UserMeasurementController;
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
Route::prefix('v1')->name('api.')->group(function () {
    Route::post('/login', [UserController::class, 'login'])->name('login');
});

// Protected routes
Route::middleware('auth:sanctum')->prefix('v1')->name('api.')->group(
    function () {
        // User related routes
        Route::post('/logout', [UserController::class, 'logout'])->name('logout');
        Route::get('/current-user', [UserController::class, 'getCurrentUser'])->name('user.current');

        // Health & Preferences
        Route::apiResource('health-data', HealthDataController::class);
        Route::apiResource('preferences', UserPreferenceController::class);
        Route::apiResource('measurements', UserMeasurementController::class);
        Route::apiResource('nutrition-types', NutritionTypeController::class);

        // Food Items & Nutrition
        Route::get('/food-items/search', [FoodItemController::class, 'search'])->name('food-items.search');
        Route::apiResource('food-items', FoodItemController::class);

        Route::get('food-nutrition/search', [FoodNutritionController::class, 'search'])->name('food-nutrition.search');
        Route::apiResource('food-nutrition', FoodNutritionController::class);

        // Meals
        Route::get('meals/search', [MealController::class, 'search'])->name('meals.search');
        Route::apiResource('meals', MealController::class);
        Route::apiResource('meal_log', UserMealLogController::class);

        // Exercises
        Route::apiResource('exercise-categories', ExerciseCategoryController::class)
            ->parameters(['exercise-categories' => 'category'])
            ->scoped(['category' => 'id']);

        Route::get('exercises/search', [ExerciseController::class, 'search'])->name('exercises.search');
        Route::apiResource('exercises', ExerciseController::class);

        // Logs
        Route::get('food-logs/daily-nutrition', [UserFoodLogController::class, 'getDailyNutrition'])->name('food-logs.daily-nutrition');
        Route::apiResource('food-logs', UserFoodLogController::class);

        Route::get('exercise-logs/stats', [UserExerciseLogController::class, 'getStats'])->name('exercise-logs.stats');
        Route::apiResource('exercise-logs', UserExerciseLogController::class);

        Route::get('/nutrition/summary', [App\Http\Controllers\Api\NutritionSummaryController::class, 'getSummary']);
        Route::get('/nutrition/recommended', [App\Http\Controllers\Api\NutritionSummaryController::class, 'getRecommendedCalories']);
        Route::get('/nutrition/consumed', [App\Http\Controllers\Api\NutritionSummaryController::class, 'getConsumedCalories']);
        Route::get('/nutrition/burned', [App\Http\Controllers\Api\NutritionSummaryController::class, 'getBurnedCalories']);
        Route::get('/nutrition/weekly', [App\Http\Controllers\Api\NutritionSummaryController::class, 'getWeeklySummary']);
    }
);
