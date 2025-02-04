<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseIntensity\StoreExerciseIntensityRequest;
use App\Http\Requests\ExerciseIntensity\UpdateExerciseIntensityRequest;
use App\Models\ExerciseIntensity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseIntensityController extends Controller
{
    public function index(): View
    {
        $intensities = ExerciseIntensity::paginate(10);
        
        return view('exercise-intensities.index', compact('intensities'));
    }

    public function create(): View
    {
        return view('exercise-intensities.create');
    }

    public function store(StoreExerciseIntensityRequest $request): RedirectResponse
    {
        ExerciseIntensity::create($request->validated());
        
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity created successfully');
    }

    public function edit(ExerciseIntensity $intensity): View
    {
        return view('exercise-intensities.edit', compact('intensity'));
    }

    public function update(UpdateExerciseIntensityRequest $request, ExerciseIntensity $intensity): RedirectResponse
    {
        $intensity->update($request->validated());
        
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity updated successfully');
    }

    public function destroy(ExerciseIntensity $intensity): RedirectResponse
    {
        $intensity->delete();
        
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity deleted successfully');
    }
}