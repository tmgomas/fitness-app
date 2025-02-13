<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserFoodLog\StoreUserFoodLogRequest;
use App\Http\Requests\UserFoodLog\UpdateUserFoodLogRequest;
use App\Http\Resources\UserFoodLogResource;
use App\Services\UserFoodLog\Interfaces\UserFoodLogServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserFoodLogController extends Controller
{
    private $userFoodLogService;

    public function __construct(UserFoodLogServiceInterface $userFoodLogService)
    {
        $this->userFoodLogService = $userFoodLogService;
    }

    public function index(Request $request)
    {
        try {
            $foodLogs = $this->userFoodLogService->getAllFoodLogs($request->all());
            return UserFoodLogResource::collection($foodLogs);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving food logs',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreUserFoodLogRequest $request)
    {
        try {
            $foodLog = $this->userFoodLogService->storeFoodLog($request->validated());
            return new UserFoodLogResource($foodLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating food log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $foodLog = $this->userFoodLogService->getFoodLog($id);
            return new UserFoodLogResource($foodLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Food log not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(UpdateUserFoodLogRequest $request, $id)
    {
        try {
            $foodLog = $this->userFoodLogService->updateFoodLog($id, $request->validated());
            return new UserFoodLogResource($foodLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating food log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userFoodLogService->deleteFoodLog($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Food log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting food log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getDailyNutrition(Request $request)
    {
        try {
            $report = $this->userFoodLogService->getDailyNutritionReport($request->all());
            return response()->json([
                'status' => 'success',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error generating nutrition report',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
