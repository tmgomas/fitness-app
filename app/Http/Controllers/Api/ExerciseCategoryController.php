<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExerciseCategory\StoreExerciseCategoryRequest;
use App\Http\Requests\ExerciseCategory\UpdateExerciseCategoryRequest;
use App\Models\ExerciseCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExerciseCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ExerciseCategory::with('exercises')->get();
        
        return response()->json([
            'data' => $categories
        ]);
    }

    public function store(StoreExerciseCategoryRequest $request): JsonResponse
    {
        $category = ExerciseCategory::create($request->validated());
        
        return response()->json([
            'message' => 'Exercise category created successfully',
            'data' => $category
        ], Response::HTTP_CREATED);
    }

    public function show(ExerciseCategory $category): JsonResponse
    {
        return response()->json([
            'data' => $category->load('exercises')
        ]);
    }

    public function update(UpdateExerciseCategoryRequest $request, ExerciseCategory $category): JsonResponse
    {
        $category->update($request->validated());
        
        return response()->json([
            'message' => 'Exercise category updated successfully',
            'data' => $category
        ]);
    }

    public function destroy(ExerciseCategory $category): JsonResponse
    {
        $category->delete();
        
        return response()->json([
            'message' => 'Exercise category deleted successfully'
        ]);
    }
}
