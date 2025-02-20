<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FoodNutrition;
use App\Models\FoodItem;
use App\Models\NutritionType;
use App\Http\Requests\FoodNutrition\StoreFoodNutritionRequest;
use App\Http\Requests\FoodNutrition\UpdateFoodNutritionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FoodNutritionController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = FoodNutrition::with(['food', 'nutritionType']);

        if ($request->filled('search')) {
            $searchTerm = $request->get('search');

            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('food', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', "%{$searchTerm}%");
                })
                    ->orWhereHas('nutritionType', function ($query) use ($searchTerm) {
                        $query->where('name', 'like', "%{$searchTerm}%");
                    })
                    ->orWhere('amount_per_100g', 'like', "%{$searchTerm}%")
                    ->orWhere('measurement_unit', 'like', "%{$searchTerm}%");
            });
        }

        // Add filters
        if ($request->filled('food_id')) {
            $query->where('food_id', $request->food_id);
        }

        if ($request->filled('nutrition_id')) {
            $query->where('nutrition_id', $request->nutrition_id);
        }

        $foodNutritions = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $foodNutritions,
            'message' => 'Food nutrition data retrieved successfully'
        ]);
    }

    // Index method for API
    public function index(Request $request): JsonResponse
    {
        $foodNutritions = FoodNutrition::with(['food', 'nutritionType'])
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $foodNutritions,
            'message' => 'Food nutrition list retrieved successfully'
        ]);
    }
    public function create(): View
    {
        $foodItems = FoodItem::where('is_active', true)->get();
        $nutritionTypes = NutritionType::all();

        return view('food-nutrition.create', compact('foodItems', 'nutritionTypes'));
    }

    public function store(StoreFoodNutritionRequest $request): RedirectResponse
    {
        FoodNutrition::create($request->validated());

        return redirect()
            ->route('food-nutrition.index')
            ->with('success', 'Food nutrition created successfully.');
    }

    public function edit(FoodNutrition $foodNutrition): View
    {
        $foodItems = FoodItem::where('is_active', true)->get();
        $nutritionTypes = NutritionType::all();

        return view('food-nutrition.edit', compact('foodNutrition', 'foodItems', 'nutritionTypes'));
    }

    public function update(UpdateFoodNutritionRequest $request, FoodNutrition $foodNutrition): RedirectResponse
    {
        $foodNutrition->update($request->validated());

        return redirect()
            ->route('food-nutrition.index')
            ->with('success', 'Food nutrition updated successfully.');
    }

    public function destroy(FoodNutrition $foodNutrition): RedirectResponse
    {
        $foodNutrition->delete();

        return redirect()
            ->route('food-nutrition.index')
            ->with('success', 'Food nutrition deleted successfully.');
    }
}
