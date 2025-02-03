<?php

use App\Http\Controllers\Api\NutritionTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Default user route from Sanctum
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Nutrition Types API Routes
Route::middleware('auth:sanctum')->group(function () {
    // List all nutrition types
    Route::get('/nutrition-types', [NutritionTypeController::class, 'index']);
    
    // Get single nutrition type
    Route::get('/nutrition-types/{nutritionType}', [NutritionTypeController::class, 'show']);
    
    // Create new nutrition type
    Route::post('/nutrition-types', [NutritionTypeController::class, 'store']);
    
    // Update nutrition type
    Route::put('/nutrition-types/{nutritionType}', [NutritionTypeController::class, 'update']);
    
    // Delete nutrition type
    Route::delete('/nutrition-types/{nutritionType}', [NutritionTypeController::class, 'destroy']);
    
    // Search nutrition types
    Route::get('/nutrition-types/search', [NutritionTypeController::class, 'search']);


    Route::get('/food-items/search', [FoodItemController::class, 'search']);
    Route::apiResource('food-items', FoodItemController::class);
});

// Public routes (if needed)
Route::get('/health-check', function () {
    return response()->json(['status' => 'ok']);
});