<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMealLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserMealLogController extends Controller
{
    /**
     * Display a listing of the meal logs.
     */
    public function index(Request $request)
    {
        $query = UserMealLog::query();

        // Filter by user_id if provided
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range if provided
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        $mealLogs = $query->paginate(15);
        
        return response()->json([
            'status' => 'success',
            'data' => $mealLogs
        ]);
    }

    /**
     * Store a new meal log.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,id',
            'meal_id' => 'required|string|exists:meals,meal_id',
            'date' => 'required|date',
            'meal_type' => 'required|string',
            'serving_size' => 'required|numeric',
            'serving_unit' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mealLog = UserMealLog::create([
            'log_id' => (string) Str::uuid(),
            'user_id' => $request->user_id,
            'meal_id' => $request->meal_id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
            'serving_size' => $request->serving_size,
            'serving_unit' => $request->serving_unit,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Meal log created successfully',
            'data' => $mealLog
        ], 201);
    }

    /**
     * Display the specified meal log.
     */
    public function show($log_id)
    {
        $mealLog = UserMealLog::find($log_id);

        if (!$mealLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meal log not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $mealLog
        ]);
    }

    /**
     * Update the specified meal log.
     */
    public function update(Request $request, $log_id)
    {
        $mealLog = UserMealLog::find($log_id);

        if (!$mealLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meal log not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'meal_id' => 'string|exists:meals,meal_id',
            'date' => 'date',
            'meal_type' => 'string',
            'serving_size' => 'numeric',
            'serving_unit' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $mealLog->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Meal log updated successfully',
            'data' => $mealLog
        ]);
    }

    /**
     * Remove the specified meal log.
     */
    public function destroy($log_id)
    {
        $mealLog = UserMealLog::find($log_id);

        if (!$mealLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Meal log not found'
            ], 404);
        }

        $mealLog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Meal log deleted successfully'
        ]);
    }
}
