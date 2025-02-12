<?php

// app/Http/Controllers/ExerciseIntensityController.php

namespace App\Http\Controllers;

use App\Http\Requests\ExerciseIntensity\StoreExerciseIntensityRequest;
use App\Http\Requests\ExerciseIntensity\UpdateExerciseIntensityRequest;
use App\Services\ExerciseIntensity\Interfaces\ExerciseIntensityServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ExerciseIntensityController extends Controller
{
    private $exerciseIntensityService;

    public function __construct(ExerciseIntensityServiceInterface $exerciseIntensityService)
    {
        $this->exerciseIntensityService = $exerciseIntensityService;
    }

    public function index(): View
    {

        $intensities = $this->exerciseIntensityService->getPaginatedIntensities(10);
        return view('exercise-intensities.index', compact('intensities'));
    }

    public function create(): View
    {
        return view('exercise-intensities.create');
    }

    public function store(StoreExerciseIntensityRequest $request): RedirectResponse
    {
        $this->exerciseIntensityService->createIntensity($request->validated());
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity created successfully');
    }

    public function edit($id): View
    {
        $intensity = $this->exerciseIntensityService->findIntensity($id);
        return view('exercise-intensities.edit', compact('intensity'));
    }

    public function update(UpdateExerciseIntensityRequest $request, $id): RedirectResponse
    {
        $this->exerciseIntensityService->updateIntensity($id, $request->validated());
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $this->exerciseIntensityService->deleteIntensity($id);
        return redirect()
            ->route('exercise-intensities.index')
            ->with('success', 'Exercise intensity deleted successfully');
    }
}
