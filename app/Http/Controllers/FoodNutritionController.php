<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FoodNutrition;
use App\Models\FoodItem;
use App\Models\NutritionType;
use App\Http\Requests\FoodNutrition\StoreFoodNutritionRequest;
use App\Http\Requests\FoodNutrition\UpdateFoodNutritionRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class FoodNutritionController extends Controller
{
    public function index(Request $request): View
    {
        $query = FoodNutrition::with(['food', 'nutritionType']);
        

        // Search
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->whereHas('food', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            })
            ->orWhereHas('nutritionType', function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%");
            });
        }

        $foodNutritions = $query->latest()->paginate(10);
        // dd($foodNutritions);
        return view('food-nutrition.index', compact('foodNutritions'));
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