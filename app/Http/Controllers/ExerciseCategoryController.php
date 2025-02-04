<?php

namespace App\Http\Controllers;  // Changed from App\Http\Controllers

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseCategory\StoreExerciseCategoryRequest;
use App\Http\Requests\ExerciseCategory\UpdateExerciseCategoryRequest;
use App\Models\ExerciseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseCategoryController extends Controller
{
    public function index(): View
    {
        $categories = ExerciseCategory::with('exercises')->paginate(10);

        return view('exercise-categories.index', compact('categories')); // Added '' prefix
    }

    public function create(): View
    {
        return view('exercise-categories.create'); // Added '' prefix
    }

    public function store(StoreExerciseCategoryRequest $request): RedirectResponse
    {
        ExerciseCategory::create($request->validated());

        return redirect()
            ->route('exercise-categories.index') // Added '' prefix
            ->with('success', 'Exercise category created successfully');
    }

    public function edit(ExerciseCategory $exerciseCategory): View  // Changed parameter name
    {
        return view('exercise-categories.edit', compact('exerciseCategory')); // Changed variable name and added '' prefix
    }

    public function update(UpdateExerciseCategoryRequest $request, ExerciseCategory $exerciseCategory): RedirectResponse // Changed parameter name
    {
        $exerciseCategory->update($request->validated());

        return redirect()
            ->route('exercise-categories.index') // Added '' prefix
            ->with('success', 'Exercise category updated successfully');
    }

    public function destroy(ExerciseCategory $exerciseCategory): RedirectResponse // Changed parameter name
    {
        $exerciseCategory->delete();

        return redirect()
            ->route('exercise-categories.index') // Added '' prefix
            ->with('success', 'Exercise category deleted successfully');
    }
}
