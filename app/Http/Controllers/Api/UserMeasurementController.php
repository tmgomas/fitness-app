<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Measurement\StoreMeasurementRequest;
use App\Http\Requests\Measurement\UpdateMeasurementRequest;
use App\Http\Resources\MeasurementResource;
use App\Services\Measurement\Interfaces\MeasurementServiceInterface;
use Illuminate\Http\JsonResponse;

class UserMeasurementController extends Controller
{
    private $measurementService;

    public function __construct(MeasurementServiceInterface $measurementService)
    {
        $this->measurementService = $measurementService;
    }

    public function index(): JsonResponse
    {
        try {
            $measurements = $this->measurementService->getAllMeasurements();
            return response()->json([
                'success' => true,
                'data' => MeasurementResource::collection($measurements)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving measurements'
            ], 500);
        }
    }

    public function store(StoreMeasurementRequest $request): JsonResponse
    {
        try {
            $measurement = $this->measurementService->createMeasurement($request->validated());
            return response()->json([
                'success' => true,
                'data' => new MeasurementResource($measurement)
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating measurement'
            ], 500);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $measurement = $this->measurementService->getMeasurement($id);
            return response()->json([
                'success' => true,
                'data' => new MeasurementResource($measurement)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Measurement not found'
            ], 404);
        }
    }

    public function update(UpdateMeasurementRequest $request, string $id): JsonResponse
    {
        try {
            $measurement = $this->measurementService->updateMeasurement($id, $request->validated());
            return response()->json([
                'success' => true,
                'data' => new MeasurementResource($measurement)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating measurement'
            ], 500);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->measurementService->deleteMeasurement($id);
            return response()->json([
                'success' => true,
                'message' => 'Measurement deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting measurement'
            ], 500);
        }
    }
}
