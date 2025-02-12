<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NutritionTypeResource;
use App\Http\Requests\NutritionType\StoreNutritionTypeRequest;
use App\Http\Requests\NutritionType\UpdateNutritionTypeRequest;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Exception;

class NutritionTypeController extends Controller
{
    protected $nutritionTypeService;

    public function __construct(NutritionTypeServiceInterface $nutritionTypeService)
    {
        $this->nutritionTypeService = $nutritionTypeService;
    }

    public function index(): JsonResponse
    {
        try {
            $nutritionTypes = $this->nutritionTypeService->getAllNutritionTypes();
            return response()->json([
                'status' => 'success',
                'data' => NutritionTypeResource::collection($nutritionTypes)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(StoreNutritionTypeRequest $request): JsonResponse
    {
        try {
            $nutritionType = $this->nutritionTypeService->createNutritionType($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Nutrition type created successfully',
                'data' => new NutritionTypeResource($nutritionType)
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $nutritionId): JsonResponse
    {
        try {
            $nutritionType = $this->nutritionTypeService->getNutritionType($nutritionId);
            return response()->json([
                'status' => 'success',
                'data' => new NutritionTypeResource($nutritionType)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function update(UpdateNutritionTypeRequest $request, string $nutritionId): JsonResponse
    {
        try {
            $nutritionType = $this->nutritionTypeService->updateNutritionType($nutritionId, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Nutrition type updated successfully',
                'data' => new NutritionTypeResource($nutritionType)
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $nutritionId): JsonResponse
    {
        try {
            $this->nutritionTypeService->deleteNutritionType($nutritionId);
            return response()->json([
                'status' => 'success',
                'message' => 'Nutrition type deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
