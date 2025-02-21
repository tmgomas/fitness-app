<?php

use App\Http\Controllers\ExerciseCategoryController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\ExerciseIntensityController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Web\NutritionTypeController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\FoodNutritionController;
use App\Http\Controllers\Web\MealController;
use Illuminate\Support\Facades\Route;

// Welcome route
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware(['auth', 'verified']);

// Dashboard route
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::middleware(['auth', 'admin'])->group(function () {
    // Users Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');


    Route::resource('nutrition-types', NutritionTypeController::class);

    Route::resource('food-items', FoodItemController::class);

    // Route::resource('food-nutrition', FoodNutritionController::class);

    Route::resource('exercise-categories', ExerciseCategoryController::class)->parameters([
        'exercise-categories' => 'category'
    ])->scoped([
        'category' => 'id'
    ]);

    Route::resource('exercises', ExerciseController::class);
    Route::post('exercises/{exercise}/toggle-status', [ExerciseController::class, 'toggleStatus'])->name('exercises.toggle-status');
    Route::get('exercises/search', [ExerciseController::class, 'search'])->name('exercises.search');
    Route::get('exercises/category/{categoryId}', [ExerciseController::class, 'getByCategory'])->name('exercises.by-category');
    Route::resource('exercise-intensities', ExerciseIntensityController::class);

    Route::resource('food-nutrition', FoodNutritionController::class);

    Route::resource('meals', MealController::class);
});
