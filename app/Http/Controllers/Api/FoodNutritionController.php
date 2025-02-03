<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodNutrition;
use App\Http\Requests\FoodNutrition\StoreFoodNutritionRequest;
use App\Http\Requests\FoodNutrition\UpdateFoodNutritionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodNutritionController extends Controller
{
    public function index(): JsonResponse
    {
        $foodNutritions = FoodNutrition::with(['food', 'nutritionType'])
            ->latest()
            ->paginate(10);

        return response()->json($foodNutritions);
    }

    public function store(StoreFoodNutritionRequest $request): JsonResponse
    {
        $foodNutrition = FoodNutrition::create($request->validated());

        return response()->json([
            'message' => 'Food nutrition created successfully',
            'data' => $foodNutrition->load(['food', 'nutritionType'])
        ], 201);
    }

    public function show(FoodNutrition $foodNutrition): JsonResponse
    {
        return response()->json([
            'data' => $foodNutrition->load(['food', 'nutritionType'])
        ]);
    }

    public function update(UpdateFoodNutritionRequest $request, FoodNutrition $foodNutrition): JsonResponse
    {
        $foodNutrition->update($request->validated());

        return response()->json([
            'message' => 'Food nutrition updated successfully',
            'data' => $foodNutrition->load(['food', 'nutritionType'])
        ]);
    }

    public function destroy(FoodNutrition $foodNutrition): JsonResponse
    {
        $foodNutrition->delete();

        return response()->json([
            'message' => 'Food nutrition deleted successfully'
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        
        $foodNutritions = FoodNutrition::with(['food', 'nutritionType'])
            ->whereHas('food', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('nutritionType', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(10);

        return response()->json($foodNutritions);
    }
}