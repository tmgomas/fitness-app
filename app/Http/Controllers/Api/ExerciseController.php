<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Http\Resources\ExerciseResource;
use App\Models\Exercise;
use App\Services\Exercise\Interfaces\ExerciseServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExerciseController extends Controller
{
    private ExerciseServiceInterface $exerciseService;

    public function __construct(ExerciseServiceInterface $exerciseService)
    {
        $this->exerciseService = $exerciseService;
    }

    public function index(): JsonResponse
    {
        $exercises = $this->exerciseService->getAllExercises();
        return response()->json(ExerciseResource::collection($exercises));
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $exercise = $this->exerciseService->createExercise(
                $request->validated(),
                $request->hasFile('image') ? $request->file('image') : null
            );

            DB::commit();

            return response()->json([
                'message' => 'Exercise created successfully',
                'exercise' => new ExerciseResource($exercise)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error creating exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Exercise $exercise): JsonResponse
    {
        return response()->json(new ExerciseResource($exercise->load('category')));
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        try {
            DB::beginTransaction();

            Log::info('Update Exercise - Start', [
                'exercise_id' => $exercise->id,
                'request_data' => $request->all()
            ]);

            $validated = $request->validated();
            Log::info('Validated Data', ['validated' => $validated]);

            // Handle image separately since it's optional
            $validated = array_filter($validated, function ($value) {
                return $value !== null;
            });
            Log::info('Filtered Data', ['filtered' => $validated]);

            $hasFile = $request->hasFile('image');
            Log::info('Image Upload Check', ['has_file' => $hasFile]);

            $updatedExercise = $this->exerciseService->updateExercise(
                $exercise->id,
                $validated,
                $request->hasFile('image') ? $request->file('image') : null
            );

            DB::commit();

            return response()->json([
                'message' => 'Exercise updated successfully',
                'exercise' => $updatedExercise
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Error updating exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        try {
            DB::beginTransaction();

            $this->exerciseService->deleteExercise($exercise);

            DB::commit();

            return response()->json(['message' => 'Exercise deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting exercise',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $exercises = $this->exerciseService->searchExercises($query);

        return response()->json([
            'data' => ExerciseResource::collection($exercises),
            'meta' => [
                'current_page' => $exercises->currentPage(),
                'last_page' => $exercises->lastPage(),
                'per_page' => $exercises->perPage(),
                'total' => $exercises->total()
            ]
        ]);
    }

    public function getByCategory(Request $request, string $categoryId): JsonResponse
    {
        $exercises = $this->exerciseService->getByCategory($categoryId);
        return response()->json(ExerciseResource::collection($exercises));
    }

    public function toggleStatus(Exercise $exercise): JsonResponse
    {
        try {
            $exercise = $this->exerciseService->toggleStatus($exercise);

            return response()->json([
                'message' => 'Exercise status updated successfully',
                'is_active' => $exercise->is_active
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating exercise status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
