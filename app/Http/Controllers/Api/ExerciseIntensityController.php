<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseIntensity\StoreExerciseIntensityRequest;
use App\Http\Requests\ExerciseIntensity\UpdateExerciseIntensityRequest;
use App\Models\ExerciseIntensity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExerciseIntensityController extends Controller
{
    public function index(): JsonResponse
    {
        $intensities = ExerciseIntensity::all();
        
        return response()->json([
            'data' => $intensities
        ]);
    }

    public function store(StoreExerciseIntensityRequest $request): JsonResponse
    {
        $intensity = ExerciseIntensity::create($request->validated());
        
        return response()->json([
            'message' => 'Exercise intensity created successfully',
            'data' => $intensity
        ], Response::HTTP_CREATED);
    }

    public function show(ExerciseIntensity $intensity): JsonResponse
    {
        return response()->json([
            'data' => $intensity
        ]);
    }

    public function update(UpdateExerciseIntensityRequest $request, ExerciseIntensity $intensity): JsonResponse
    {
        $intensity->update($request->validated());
        
        return response()->json([
            'message' => 'Exercise intensity updated successfully',
            'data' => $intensity
        ]);
    }

    public function destroy(ExerciseIntensity $intensity): JsonResponse
    {
        $intensity->delete();
        
        return response()->json([
            'message' => 'Exercise intensity deleted successfully'
        ]);
    }
}