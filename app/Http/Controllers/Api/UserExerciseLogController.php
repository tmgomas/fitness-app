<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserExerciseLog\StoreUserExerciseLogRequest;
use App\Http\Requests\UserExerciseLog\UpdateUserExerciseLogRequest;
use App\Http\Resources\UserExerciseLogResource;
use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserExerciseLogController extends Controller
{
    private $userExerciseLogService;

    public function __construct(UserExerciseLogServiceInterface $userExerciseLogService)
    {
        $this->userExerciseLogService = $userExerciseLogService;
    }

    public function index(Request $request)
    {
        try {
            $exerciseLogs = $this->userExerciseLogService->getAllExerciseLogs($request->all());
            return UserExerciseLogResource::collection($exerciseLogs);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving exercise logs',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(StoreUserExerciseLogRequest $request)
    {
        try {
            $exerciseLog = $this->userExerciseLogService->storeExerciseLog($request->validated());
            return new UserExerciseLogResource($exerciseLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error creating exercise log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $exerciseLog = $this->userExerciseLogService->getExerciseLog($id);
            return new UserExerciseLogResource($exerciseLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exercise log not found',
                'error' => $e->getMessage()
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function update(UpdateUserExerciseLogRequest $request, $id)
    {
        try {
            $exerciseLog = $this->userExerciseLogService->updateExerciseLog($id, $request->validated());
            return new UserExerciseLogResource($exerciseLog);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating exercise log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userExerciseLogService->deleteExerciseLog($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Exercise log deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting exercise log',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getStats(Request $request)
    {
        try {
            $stats = $this->userExerciseLogService->getExerciseStats([
                'start_date' => $request->get('start_date'),
                'end_date' => $request->get('end_date')
            ]);

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error generating exercise stats',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
