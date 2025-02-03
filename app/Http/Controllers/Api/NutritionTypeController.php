<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NutritionType;
use App\Http\Requests\NutritionType\StoreNutritionTypeRequest;
use App\Http\Requests\NutritionType\UpdateNutritionTypeRequest;
use Illuminate\Http\JsonResponse;

class NutritionTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $nutritionTypes = NutritionType::where('is_active', true)->get();
        return response()->json(['data' => $nutritionTypes]);
    }

    public function store(StoreNutritionTypeRequest $request): JsonResponse
    {
        $nutritionType = NutritionType::create($request->validated());
        return response()->json([
            'message' => 'Nutrition type created successfully',
            'data' => $nutritionType
        ], 201);
    }

    public function show(NutritionType $nutritionType): JsonResponse
    {
        return response()->json(['data' => $nutritionType]);
    }

    public function update(UpdateNutritionTypeRequest $request, NutritionType $nutritionType): JsonResponse
    {
        $nutritionType->update($request->validated());
        return response()->json([
            'message' => 'Nutrition type updated successfully',
            'data' => $nutritionType
        ]);
    }

    public function destroy(NutritionType $nutritionType): JsonResponse
    {
        $nutritionType->delete();
        return response()->json([
            'message' => 'Nutrition type deleted successfully'
        ]);
    }
}