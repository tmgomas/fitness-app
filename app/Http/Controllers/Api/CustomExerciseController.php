<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomExercise\StoreCustomExerciseRequest;
use App\Http\Requests\CustomExercise\UpdateCustomExerciseRequest;
use App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CustomExerciseController extends Controller
{
    protected $customExerciseService;

    public function __construct(CustomExerciseServiceInterface $customExerciseService)
    {
        $this->customExerciseService = $customExerciseService;
    }

    public function index(): JsonResponse
    {
        try {
            $exercises = $this->customExerciseService->getAllCustomExercises();
            return response()->json([
                'status' => 'success',
                'data' => $exercises
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving custom exercises',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreCustomExerciseRequest $request): JsonResponse
    {
        try {
            $exercise = $this->customExerciseService->createCustomExercise($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Custom exercise created successfully',
                'data' => $exercise
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating custom exercise',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $exercise = $this->customExerciseService->getCustomExercise($id);
            return response()->json([
                'status' => 'success',
                'data' => $exercise
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Custom exercise not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(UpdateCustomExerciseRequest $request, $id): JsonResponse
    {
        try {
            $exercise = $this->customExerciseService->updateCustomExercise($id, $request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Custom exercise updated successfully',
                'data' => $exercise
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating custom exercise',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->customExerciseService->deleteCustomExercise($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Custom exercise deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting custom exercise',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
