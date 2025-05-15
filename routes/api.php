<?php

use App\Http\Controllers\Api\CustomExerciseController;
use App\Http\Controllers\Api\ExerciseCategoryController;
use App\Http\Controllers\Api\ExerciseController;
use App\Http\Controllers\Api\FoodItemController;
use App\Http\Controllers\Api\HealthDataController;
use App\Http\Controllers\Api\MealController;
use App\Http\Controllers\Api\NutritionSummaryController;
use App\Http\Controllers\Api\NutritionTypeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UserExerciseLogController;
use App\Http\Controllers\Api\UserFoodLogController;
use App\Http\Controllers\Api\UserMealLogController;
use App\Http\Controllers\Api\UserMeasurementController;
use App\Http\Controllers\Api\UserPreferenceController;
use App\Http\Controllers\FoodNutritionController;
use App\Http\Controllers\Api\UserProfileController;
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
        Route::put('/update', [UserProfileController::class, 'updateProfile']);
        // Route::put('/update-password', [UserProfileController::class, 'updatePassword']);
     
        Route::post('/profile/password', [UserProfileController::class, 'updatePassword']);
        Route::post('/upload-picture', [UserProfileController::class, 'uploadProfilePicture']);
        Route::delete('/delete-picture', [UserProfileController::class, 'deleteProfilePicture']);
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
        Route::apiResource('custom-exercises', CustomExerciseController::class);
        Route::prefix('nutrition')->group(function () {
            Route::get('/summary', [NutritionSummaryController::class, 'getSummary']);
            Route::get('/recommended', [NutritionSummaryController::class, 'getRecommendedCalories']);
            Route::get('/consumed', [NutritionSummaryController::class, 'getConsumedCalories']);
            Route::get('/burned', [NutritionSummaryController::class, 'getBurnedCalories']);
            Route::get('/weekly', [NutritionSummaryController::class, 'getWeeklySummary']);
        });

        Route::get('/reports/monthly/calories/summary', [App\Http\Controllers\Api\MonthlyReportController::class, 'getMonthlyCaloriesSummary']);
        Route::get('/reports/monthly/calories/details', [App\Http\Controllers\Api\MonthlyReportController::class, 'getMonthlyCaloriesDetails']);


        // User profile routes

        Route::get('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'getProfile']);
        Route::put('/profile', [App\Http\Controllers\Api\UserProfileController::class, 'updateProfile']);
                Route::post('/profile/picture', [App\Http\Controllers\Api\UserProfileController::class, 'uploadProfilePicture']);
        Route::delete('/profile/picture', [App\Http\Controllers\Api\UserProfileController::class, 'deleteProfilePicture']);
        
         Route::prefix('agreements')->group(function () {
            Route::get('/latest', [\App\Http\Controllers\Api\AgreementController::class, 'getLatest']);
            Route::post('/accept', [\App\Http\Controllers\Api\AgreementController::class, 'accept']);
            Route::get('/check-status', [\App\Http\Controllers\Api\AgreementController::class, 'checkStatus']);
        });
    }
);
