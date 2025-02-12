<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FoodNutrition\StoreFoodNutritionRequest;
use App\Http\Requests\FoodNutrition\UpdateFoodNutritionRequest;
use App\Http\Resources\FoodNutritionResource;
use App\Services\FoodNutrition\Interfaces\FoodNutritionServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FoodNutritionController extends Controller
{
    public function __construct(
        private readonly FoodNutritionServiceInterface $foodNutritionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = ['search' => $request->get('search')];
        $foodNutritions = $this->foodNutritionService->getAllFoodNutritions($filters);

        return response()->json(FoodNutritionResource::collection($foodNutritions));
    }

    public function store(StoreFoodNutritionRequest $request): JsonResponse
    {
        $foodNutrition = $this->foodNutritionService->createFoodNutrition($request->validated());
        return response()->json(new FoodNutritionResource($foodNutrition), 201);
    }

    public function show(string $id): JsonResponse
    {
        $foodNutrition = $this->foodNutritionService->getFoodNutritionById($id);
        return response()->json(new FoodNutritionResource($foodNutrition));
    }

    public function update(UpdateFoodNutritionRequest $request, string $id): JsonResponse
    {
        $foodNutrition = $this->foodNutritionService->updateFoodNutrition($id, $request->validated());
        return response()->json(new FoodNutritionResource($foodNutrition));
    }

    public function destroy(string $id): JsonResponse
    {
        $this->foodNutritionService->deleteFoodNutrition($id);
        return response()->json(null, 204);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $foodNutritions = $this->foodNutritionService->searchFoodNutritions($query);
        return response()->json(FoodNutritionResource::collection($foodNutritions));
    }
}
