<?php

// app/Http/Controllers/Api/ExerciseIntensityController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseIntensity\StoreExerciseIntensityRequest;
use App\Http\Requests\ExerciseIntensity\UpdateExerciseIntensityRequest;
use App\Http\Resources\ExerciseIntensityResource;
use App\Services\ExerciseIntensity\Interfaces\ExerciseIntensityServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExerciseIntensityController extends Controller
{
    private $exerciseIntensityService;

    public function __construct(ExerciseIntensityServiceInterface $exerciseIntensityService)
    {
        $this->exerciseIntensityService = $exerciseIntensityService;
    }

    public function index(): JsonResponse
    {
        $intensities = $this->exerciseIntensityService->getAllIntensities();
        return response()->json(ExerciseIntensityResource::collection($intensities));
    }

    public function store(StoreExerciseIntensityRequest $request): JsonResponse
    {
        $intensity = $this->exerciseIntensityService->createIntensity($request->validated());
        return response()->json([
            'message' => 'Exercise intensity created successfully',
            'data' => new ExerciseIntensityResource($intensity)
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $intensity = $this->exerciseIntensityService->findIntensity($id);
        return response()->json(new ExerciseIntensityResource($intensity));
    }

    public function update(UpdateExerciseIntensityRequest $request, $id): JsonResponse
    {
        $intensity = $this->exerciseIntensityService->updateIntensity($id, $request->validated());
        return response()->json([
            'message' => 'Exercise intensity updated successfully',
            'data' => new ExerciseIntensityResource($intensity)
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $this->exerciseIntensityService->deleteIntensity($id);
        return response()->json([
            'message' => 'Exercise intensity deleted successfully'
        ]);
    }
}
