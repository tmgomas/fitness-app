<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\Exercise;
use App\Models\ExerciseCategory;
use App\Services\Exercise\Interfaces\ExerciseServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    private ExerciseServiceInterface $exerciseService;

    public function __construct(ExerciseServiceInterface $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    public function index(): View
    {
        $exercises = $this->exerciseService->getAllExercises();
        $categories = ExerciseCategory::all();

        return view('exercises.index', compact('exercises', 'categories'));
    }

    public function create(): View
    {
        $categories = ExerciseCategory::all();

        return view('exercises.create', compact('categories'));
    }

    public function store(StoreExerciseRequest $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            Log::info('Store Exercise - Start', [
                'request_data' => $request->all()
            ]);

            $validated = $request->validated();
            Log::info('Validated Data', ['validated' => $validated]);

            // Handle checkbox values
            $validated['requires_distance'] = $request->boolean('requires_distance');
            $validated['requires_heartrate'] = $request->boolean('requires_heartrate');
            $validated['is_active'] = $request->boolean('is_active', true);

            $exercise = $this->exerciseService->createExercise(
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );

            DB::commit();

            return redirect()
                ->route('exercises.index')
                ->with('success', 'Exercise created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('exercises.create')
                ->with('error', 'Error creating exercise: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Exercise $exercise): View
    {
        return view('exercises.show', [
            'exercise' => $exercise->load('category')
        ]);
    }

    public function edit(Exercise $exercise): View
    {
        $categories = ExerciseCategory::all();

        return view('exercises.edit', compact('exercise', 'categories'));
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): RedirectResponse
    {
        try {
            DB::beginTransaction();

            Log::info('Update Exercise - Start', [
                'exercise_id' => $exercise->id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validated();
            Log::info('Validated Data', ['validated' => $validated]);

            // Handle checkbox values explicitly to ensure proper boolean values
            $validated['requires_distance'] = $request->boolean('requires_distance');
            $validated['requires_heartrate'] = $request->boolean('requires_heartrate');
            $validated['is_active'] = $request->boolean('is_active', true);

            Log::info('Processed Validated Data', ['processed' => $validated]);

            // Get the actual exercise ID string
            $exerciseId = $exercise->getKey();
            Log::info('Exercise ID for Update', ['id' => $exerciseId]);

            $this->exerciseService->updateExercise(
                $exerciseId, // Pass the ID string directly
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );

            DB::commit();

            return redirect()
                ->route('exercises.index')
                ->with('success', 'Exercise updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('exercises.edit', $exercise)
                ->with('error', 'Error updating exercise: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Exercise $exercise): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get the actual exercise ID string
            $exerciseId = $exercise->getKey();
            $this->exerciseService->deleteExercise($exerciseId);

            DB::commit();

            return redirect()
                ->route('exercises.index')
                ->with('success', 'Exercise deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('exercises.index')
                ->with('error', 'Error deleting exercise: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Exercise $exercise): RedirectResponse
    {
        try {
            DB::beginTransaction();

            // Get the actual exercise ID string
            $exerciseId = $exercise->getKey();
            $this->exerciseService->toggleStatus($exerciseId);

            DB::commit();

            return redirect()
                ->route('exercises.index')
                ->with('success', 'Exercise status updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Toggle Status Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()
                ->route('exercises.index')
                ->with('error', 'Error updating exercise status: ' . $e->getMessage());
        }
    }
}
