<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Meal\StoreMealRequest;
use App\Http\Requests\Meal\UpdateMealRequest;
use App\Models\Meal;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MealController extends Controller
{
    public function index(): JsonResponse
    {
        $meals = Meal::with(['nutritionFacts', 'foods'])
            ->latest()
            ->paginate(10);

        return response()->json($meals);
    }

    public function store(StoreMealRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('meals', 'public');
            }

            // Create meal
            $meal = Meal::create([
                'name' => $request->name,
                'description' => $request->description,
                'image_url' => $imagePath ? Storage::url($imagePath) : null,
                'default_serving_size' => $request->default_serving_size,
                'serving_unit' => $request->serving_unit,
                'is_active' => true
            ]);

            // Create nutrition facts
            foreach ($request->nutrition_facts as $nutrition) {
                $meal->nutritionFacts()->create($nutrition);
            }

            // Create food items
            foreach ($request->foods as $food) {
                $meal->foods()->create($food);
            }

            DB::commit();

            return response()->json([
                'message' => 'Meal created successfully',
                'meal' => $meal->load(['nutritionFacts', 'foods'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($imagePath)) {
                Storage::delete($imagePath);
            }
            return response()->json([
                'message' => 'Error creating meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Meal $meal): JsonResponse
    {
        return response()->json($meal->load(['nutritionFacts', 'foods']));
    }

    public function update(UpdateMealRequest $request, Meal $meal): JsonResponse
    {
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

            // Update meal
            $meal->update($request->except('image'));

            // Update nutrition facts
            $meal->nutritionFacts()->delete();
            foreach ($request->nutrition_facts as $nutrition) {
                $meal->nutritionFacts()->create($nutrition);
            }

            // Update food items
            $meal->foods()->delete();
            foreach ($request->foods as $food) {
                $meal->foods()->create($food);
            }

            DB::commit();

            return response()->json([
                'message' => 'Meal updated successfully',
                'meal' => $meal->load(['nutritionFacts', 'foods'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error updating meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Meal $meal): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Delete image if exists
            if ($meal->image_url) {
                Storage::delete(str_replace('/storage/', '', $meal->image_url));
            }

            $meal->delete();

            DB::commit();

            return response()->json(['message' => 'Meal deleted successfully']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting meal',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}