<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserMealLog\StoreUserMealLogRequest;
use App\Http\Requests\UserMealLog\UpdateUserMealLogRequest;
use App\Http\Resources\UserMealLogResource;
use App\Services\UserMealLog\Interfaces\UserMealLogServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserMealLogController extends Controller
{
    protected $userMealLogService;

    public function __construct(UserMealLogServiceInterface $userMealLogService)
    {
        $this->userMealLogService = $userMealLogService;
    }

    /**
     * Get all meal logs with optional date filtering
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $filters = [];

            if ($request->has(['start_date', 'end_date'])) {
                $filters['start_date'] = Carbon::parse($request->start_date)->startOfDay();
                $filters['end_date'] = Carbon::parse($request->end_date)->endOfDay();
            }

            $mealLogs = $this->userMealLogService->getAllMealLogs($filters);

            return response()->json([
                'status' => 'success',
                'data' => UserMealLogResource::collection($mealLogs)
            ]);
        } catch (\Exception $e) {
            Log::error('Error in meal logs index: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving meal logs',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a new meal log
     * 
     * @param StoreUserMealLogRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserMealLogRequest $request)
    {
        try {
            $mealLog = $this->userMealLogService->storeMealLog($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Meal log created successfully',
                'data' => new UserMealLogResource($mealLog)
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            Log::error('Error creating meal log: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating meal log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get specific meal log details
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $mealLog = $this->userMealLogService->getMealLog($id);

            return response()->json([
                'status' => 'success',
                'data' => new UserMealLogResource($mealLog)
            ]);
        } catch (\Exception $e) {
            Log::error('Error retrieving meal log: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Meal log not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update existing meal log
     * 
     * @param UpdateUserMealLogRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserMealLogRequest $request, $id)
    {
        try {
            $mealLog = $this->userMealLogService->updateMealLog($id, $request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Meal log updated successfully',
                'data' => new UserMealLogResource($mealLog)
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating meal log: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating meal log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a meal log
     * 
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $this->userMealLogService->deleteMealLog($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Meal log deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting meal log: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting meal log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    
}
