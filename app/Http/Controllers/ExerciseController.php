<?php

namespace App\Http\Controllers;  // Changed namespace

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(): View
    {
        $exercises = Exercise::with('category')->paginate(10);
        $categories = ExerciseCategory::all(); // Added this line
        
        return view('exercises.index', compact('exercises', 'categories')); // Added categories to compact
    }

    public function create(): View
    {
        $categories = ExerciseCategory::all();
        
        return view('exercises.create', compact('categories'));
    }

    public function store(StoreExerciseRequest $request): RedirectResponse
    {
        Exercise::create($request->validated());
        
        return redirect()
            ->route('exercises.index') // Added '' prefix
            ->with('success', 'Exercise created successfully');
    }

    public function edit(Exercise $exercise): View
    {
        $categories = ExerciseCategory::all();
        
        return view('exercises.edit', compact('exercise', 'categories'));
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        $exercise->update($request->validated());
        
        return redirect()
            ->route('exercises.index') // Added '' prefix
            ->with('success', 'Exercise updated successfully');
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        $exercise->delete();
        
        return redirect()
            ->route('exercises.index') // Added '' prefix
            ->with('success', 'Exercise deleted successfully');
    }

    public function toggleStatus(Exercise $exercise): RedirectResponse
    {
        $exercise->update([
            'is_active' => !$exercise->is_active
        ]);
        
        return redirect()
            ->route('exercises.index') // Added '' prefix
            ->with('success', 'Exercise status updated successfully');
    }
}