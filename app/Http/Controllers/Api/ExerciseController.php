<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exercise\StoreExerciseRequest;
use App\Http\Requests\Exercise\UpdateExerciseRequest;
use App\Models\Exercise;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExerciseController extends Controller
{
    public function index(): JsonResponse
    {
        $exercises = Exercise::with('category')->get();
        
        return response()->json([
            'data' => $exercises
        ]);
    }

    public function store(StoreExerciseRequest $request): JsonResponse
    {
        $exercise = Exercise::create($request->validated());
        
        return response()->json([
            'message' => 'Exercise created successfully',
            'data' => $exercise->load('category')
        ], Response::HTTP_CREATED);
    }

    public function show(Exercise $exercise): JsonResponse
    {
        return response()->json([
            'data' => $exercise->load('category')
        ]);
    }

    public function update(UpdateExerciseRequest $request, Exercise $exercise): JsonResponse
    {
        $exercise->update($request->validated());
        
        return response()->json([
            'message' => 'Exercise updated successfully',
            'data' => $exercise->load('category')
        ]);
    }

    public function destroy(Exercise $exercise): JsonResponse
    {
        $exercise->delete();
        
        return response()->json([
            'message' => 'Exercise deleted successfully'
        ]);
    }

    public function toggleStatus(Exercise $exercise): JsonResponse
    {
        $exercise->update([
            'is_active' => !$exercise->is_active
        ]);
        
        return response()->json([
            'message' => 'Exercise status updated successfully',
            'data' => $exercise
        ]);
    }
}