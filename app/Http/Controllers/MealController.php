<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Models\Meal;
use App\Models\FoodItem;
use App\Models\NutritionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    /**
     * Display a listing of the meals.
     */
    public function index(Request $request)
    {
        $query = Meal::with(['nutritionFacts.nutritionType', 'foods.foodItem']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Status filter
        if ($request->has('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $meals = $query->latest()->paginate(10);

        return view('meals.index', compact('meals'));
    }

    /**
     * Show the form for creating a new meal.
     */
    public function create()
    {
        $foodItems = FoodItem::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $nutritionTypes = NutritionType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('meals.create', compact('foodItems', 'nutritionTypes'));
    }

    /**
     * Store a newly created meal in storage.
     */
    public function store(StoreMealRequest $request)
    {
        Log::info('Creating new meal', ['data' => $request->validated()]);

        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                Log::info('Uploading meal image');
                $imagePath = $request->file('image')->store('meals', 'public');
            }

            // Create meal
            $meal = Meal::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath ? Storage::url($imagePath) : null,
                'default_serving_size' => $request->default_serving_size,
                'serving_unit' => $request->serving_unit,
                'is_active' => $request->boolean('is_active')
            ]);

            Log::info('Meal created', ['meal_id' => $meal->meal_id]);

            // Create nutrition facts
            foreach ($request->nutrition_facts as $nutrition) {
                if (!is_null($nutrition['amount_per_100g'])) {
                    $meal->nutritionFacts()->create([
                        'nutrition_id' => $nutrition['nutrition_id'],
                        'amount_per_100g' => $nutrition['amount_per_100g'],
                        'measurement_unit' => $nutrition['measurement_unit']
                    ]);
                }
            }

            // Create food items
            foreach ($request->foods as $food) {
                $meal->foods()->create([
                    'food_id' => $food['food_id'],
                    'quantity' => $food['quantity'],
                    'unit' => $food['unit']
                ]);
            }

            DB::commit();
            Log::info('Meal creation completed successfully');

            return redirect()->route('meals.index')
                ->with('success', 'Meal created successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating meal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (isset($imagePath)) {
                Storage::delete($imagePath);
            }
            
            return back()
                ->withInput()
                ->with('error', 'Error creating meal: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified meal.
     */
    public function show(Meal $meal)
    {
        $meal->load(['nutritionFacts.nutritionType', 'foods.foodItem']);
        return view('meals.show', compact('meal'));
    }

    /**
     * Show the form for editing the specified meal.
     */
    public function edit(Meal $meal)
    {
        $meal->load(['nutritionFacts.nutritionType', 'foods.foodItem']);
        
        $foodItems = FoodItem::where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $nutritionTypes = NutritionType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('meals.edit', compact('meal', 'foodItems', 'nutritionTypes'));
    }

    /**
     * Update the specified meal in storage.
     */
    public function update(UpdateMealRequest $request, Meal $meal)
    {
        Log::info('Updating meal', [
            'meal_id' => $meal->meal_id,
            'data' => $request->validated()
        ]);

        try {
            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($meal->image_url) {
                    Storage::delete(str_replace('/storage/', '', $meal->image_url));
                }
                $imagePath = $request->file('image')->store('meals', 'public');
                $meal->image_url = Storage::url($imagePath);
            }

            // Update basic information
            $meal->update([
                'name' => $request->name,
                'description' => $request->description,
                'default_serving_size' => $request->default_serving_size,
                'serving_unit' => $request->serving_unit,
                'is_active' => $request->boolean('is_active')
            ]);

            // Update nutrition facts
            $meal->nutritionFacts()->delete();
            foreach ($request->nutrition_facts as $nutrition) {
                if (!is_null($nutrition['amount_per_100g'])) {
                    $meal->nutritionFacts()->create([
                        'nutrition_id' => $nutrition['nutrition_id'],
                        'amount_per_100g' => $nutrition['amount_per_100g'],
                        'measurement_unit' => $nutrition['measurement_unit']
                    ]);
                }
            }

            // Update food items
            $meal->foods()->delete();
            foreach ($request->foods as $food) {
                $meal->foods()->create([
                    'food_id' => $food['food_id'],
                    'quantity' => $food['quantity'],
                    'unit' => $food['unit']
                ]);
            }

            DB::commit();
            Log::info('Meal update completed successfully');

            return redirect()->route('meals.index')
                ->with('success', 'Meal updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating meal', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()
                ->withInput()
                ->with('error', 'Error updating meal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified meal from storage.
     */
    public function destroy(Meal $meal)
    {
        try {
            DB::beginTransaction();

            // Delete image if exists
            if ($meal->image_url) {
                Storage::delete(str_replace('/storage/', '', $meal->image_url));
            }

            // Delete meal (relationships will be deleted due to cascade)
            $meal->delete();

            DB::commit();
            Log::info('Meal deleted successfully', ['meal_id' => $meal->meal_id]);

            return redirect()->route('meals.index')
                ->with('success', 'Meal deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting meal', [
                'meal_id' => $meal->meal_id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Error deleting meal: ' . $e->getMessage());
        }
    }
}