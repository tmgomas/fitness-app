<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function index(): JsonResponse
    {
        $foodItems = FoodItem::where('is_active', true)
            ->latest()
            ->paginate(10);

        return response()->json($foodItems);
    }

    public function store(StoreFoodItemRequest $request): JsonResponse
    {
        $foodItem = FoodItem::create($request->validated());

        return response()->json([
            'message' => 'Food item created successfully',
            'data' => $foodItem
        ], 201);
    }

    public function show(FoodItem $foodItem): JsonResponse
    {
        return response()->json([
            'data' => $foodItem
        ]);
    }

    public function update(UpdateFoodItemRequest $request, FoodItem $foodItem): JsonResponse
    {
        $foodItem->update($request->validated());

        return response()->json([
            'message' => 'Food item updated successfully',
            'data' => $foodItem
        ]);
    }

    public function destroy(FoodItem $foodItem): JsonResponse
    {
        $foodItem->delete();

        return response()->json([
            'message' => 'Food item deleted successfully'
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', ''); // Default to empty string if no query

        $foodItems = FoodItem::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->latest()
            ->paginate(10);

        return response()->json([
            'data' => $foodItems->items(),
            'current_page' => $foodItems->currentPage(),
            'last_page' => $foodItems->lastPage(),
            'per_page' => $foodItems->perPage(),
            'total' => $foodItems->total()
        ]);
    }
}
