<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodItem\StoreFoodItemRequest;
use App\Http\Requests\FoodItem\UpdateFoodItemRequest;
use App\Http\Resources\FoodItemResource;
use App\Services\FoodItem\Interfaces\FoodItemServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodItemController extends Controller
{
    public function __construct(
        private readonly FoodItemServiceInterface $foodItemService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status')
        ];

        $foodItems = $this->foodItemService->getAllFoodItems($filters);

        // Log කිරීම
        \Illuminate\Support\Facades\Log::info('Food items data:', [
            'count' => $foodItems->count(),
            'first_item' => $foodItems->first(),
            'has_food_nutrition' => $foodItems->first() ? $foodItems->first()->relationLoaded('foodNutrition') : 'No items'
        ]);

        return response()->json(FoodItemResource::collection($foodItems));
    }

    public function store(StoreFoodItemRequest $request): JsonResponse
    {
        $foodItem = $this->foodItemService->createFoodItem($request->validated());
        return response()->json(new FoodItemResource($foodItem), 201);
    }

    public function show(string $id): JsonResponse
    {
        $foodItem = $this->foodItemService->getFoodItemById($id);

        // Log කිරීම
        \Illuminate\Support\Facades\Log::info('Food item details:', [
            'food_id' => $foodItem->food_id,
            'has_food_nutrition' => $foodItem->relationLoaded('foodNutrition'),
            'food_nutrition_count' => $foodItem->foodNutrition ? $foodItem->foodNutrition->count() : 'Relationship not loaded'
        ]);

        return response()->json(new FoodItemResource($foodItem));
    }

    public function update(UpdateFoodItemRequest $request, string $id): JsonResponse
    {
        $foodItem = $this->foodItemService->updateFoodItem($id, $request->validated());
        return response()->json(new FoodItemResource($foodItem));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->foodItemService->deleteFoodItem($id);
        return response()->json(null, 204);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $foodItems = $this->foodItemService->searchFoodItems($query);
        return response()->json(FoodItemResource::collection($foodItems));
    }
}
