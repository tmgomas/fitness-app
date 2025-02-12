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

Route::get('/', function () {
    return view('welcome');
});

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



    Route::get('exercise-categories', [ExerciseCategoryController::class, 'index'])->name('exercise-categories.index');
    Route::get('exercise-categories/create', [ExerciseCategoryController::class, 'create'])->name('exercise-categories.create');
    Route::post('exercise-categories', [ExerciseCategoryController::class, 'store'])->name('exercise-categories.store');
    Route::get('exercise-categories/{exerciseCategory}/edit', [ExerciseCategoryController::class, 'edit'])->name('exercise-categories.edit');
    Route::put('exercise-categories/{exerciseCategory}', [ExerciseCategoryController::class, 'update'])->name('exercise-categories.update');
    Route::delete('exercise-categories/{exerciseCategory}', [ExerciseCategoryController::class, 'destroy'])->name('exercise-categories.destroy');

    // Exercises
    Route::post('exercises/{exercise}/toggle-status', [ExerciseController::class, 'toggleStatus'])
        ->name('exercises.toggle-status');
    Route::resource('exercises', ExerciseController::class);

    // Exercise Intensities
    Route::resource('exercise-intensities', ExerciseIntensityController::class);

    Route::resource('food-nutrition', FoodNutritionController::class);

    Route::resource('meals', MealController::class);
});
