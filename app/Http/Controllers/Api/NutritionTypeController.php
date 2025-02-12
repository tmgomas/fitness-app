<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NutritionTypeResource;
use App\Http\Requests\NutritionType\StoreNutritionTypeRequest;
use App\Http\Requests\NutritionType\UpdateNutritionTypeRequest;
use App\Services\NutritionType\Interfaces\NutritionTypeServiceInterface;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;

class NutritionTypeController extends Controller
{
    protected $nutritionTypeService;

    public function __construct(NutritionTypeServiceInterface $nutritionTypeService)
    {
        $this->nutritionTypeService = $nutritionTypeService;
    }

    // NutritionTypeController.php
    public function index(): JsonResponse
    {
        try {
            Log::info('Starting to fetch all nutrition types');

            $nutritionTypes = $this->nutritionTypeService->getAllNutritionTypes();

            Log::info('Successfully retrieved nutrition types', [
                'count' => $nutritionTypes->count(),  // Add total count
                'status' => 'success'
            ]);

            return response()->json([
                'status' => 'success',
                'data' => NutritionTypeResource::collection($nutritionTypes),
                'meta' => [
                    'current_page' => $nutritionTypes->currentPage(),
                    'per_page' => $nutritionTypes->perPage(),
                    'total' => $nutritionTypes->total()
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Error while fetching nutrition types', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

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
