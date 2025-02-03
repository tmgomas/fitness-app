<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use App\Models\FoodNutrition;
use App\Models\NutritionType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class FoodItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = FoodItem::query();

        // Search
        if ($request->has('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Status filter
        if ($request->has('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $foodItems = $query->latest()->paginate(10);

        return view('food-items.index', compact('foodItems'));
    }

    public function create(): View
    {
        $nutritionTypes = NutritionType::all();
        return view('food-items.create', compact('nutritionTypes'));
    }

    public function store(StoreFoodItemRequest $request): RedirectResponse
{
    try {
        DB::beginTransaction();

        // Debug request data
        // dd($request->all(), $request->validated());

        // Create the food item
        $foodItem = FoodItem::create($request->validated());

        // Store nutrition information
        if ($request->has('nutrition')) {
            foreach ($request->nutrition as $nutrition) {
                // Skip if amount is not provided
                if (empty($nutrition['amount_per_100g'])) {
                    continue;
                }

                // Debug nutrition data
                // dd($nutrition);

                FoodNutrition::create([
                    'food_id' => $foodItem->food_id,
                    'nutrition_id' => $nutrition['nutrition_id'],
                    'amount_per_100g' => $nutrition['amount_per_100g'],
                    'measurement_unit' => $nutrition['measurement_unit']
                ]);
            }
        }

        DB::commit();
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item created successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Debug error
        // dd($e->getMessage());
        
        return redirect()
            ->back()
            ->with('error', 'Error creating food item. Please try again: ' . $e->getMessage())
            ->withInput();
    }
}
public function edit(FoodItem $foodItem): View
{
    // Load food item with its nutrition information
    $foodItem->load('foodNutrition');
    
    // Get active nutrition types
    $nutritionTypes = NutritionType::where('is_active', true)->get();

    // Get existing nutrition data indexed by nutrition_id
    $existingNutritionData = $foodItem->foodNutrition->keyBy('nutrition_id');
    
    // Debug if needed
    // dd($foodItem->toArray(), $existingNutritionData->toArray());

    return view('food-items.edit', compact('foodItem', 'nutritionTypes', 'existingNutritionData'));
}

    public function update(UpdateFoodItemRequest $request, FoodItem $foodItem): RedirectResponse
{
    try {
        DB::beginTransaction();

        // Update the food item
        $foodItem->update($request->validated());

        // Update nutrition information
        if ($request->has('nutrition')) {
            foreach ($request->nutrition as $nutrition) {
                // Skip if amount is not provided
                if (empty($nutrition['amount_per_100g'])) {
                    continue;
                }

                // If there's an existing food_nutrition_id, update that record
                if (!empty($nutrition['food_nutrition_id'])) {
                    FoodNutrition::where('food_nutrition_id', $nutrition['food_nutrition_id'])
                        ->update([
                            'amount_per_100g' => $nutrition['amount_per_100g'],
                            'measurement_unit' => $nutrition['measurement_unit']
                        ]);
                } else {
                    // Create new nutrition record
                    FoodNutrition::create([
                        'food_id' => $foodItem->food_id,  // Changed from $foodItem->id to $foodItem->food_id
                        'nutrition_id' => $nutrition['nutrition_id'],
                        'amount_per_100g' => $nutrition['amount_per_100g'],
                        'measurement_unit' => $nutrition['measurement_unit']
                    ]);
                }
            }
        }

        DB::commit();
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item updated successfully.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        // Debug if needed
        // dd($e->getMessage());
        
        return redirect()
            ->back()
            ->with('error', 'Error updating food item: ' . $e->getMessage())
            ->withInput();
    }
}

    public function destroy(FoodItem $foodItem): RedirectResponse
    {
        $foodItem->delete();
        
        return redirect()
            ->route('food-items.index')
            ->with('success', 'Food item deleted successfully.');
    }
}