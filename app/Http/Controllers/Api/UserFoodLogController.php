<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserFoodLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserFoodLogController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of food logs.
     */
    public function index(Request $request)
    {
        try {
            $query = UserFoodLog::where('user_id', Auth::id());

            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();

                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $foodLogs = $query->get();

            Log::info('Food Logs Query:', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'count' => $foodLogs->count()
            ]);

            return response()->json(['data' => $foodLogs]);
        } catch (\Exception $e) {
            Log::error('Error in food logs index: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    /**
     * Store a new food log.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,id',
            'food_id' => 'required|string|exists:food_items,food_id',
            'date' => 'required|date',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'required|numeric|min:0',
            'serving_unit' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $foodLog = UserFoodLog::create([
            'food_log_id' => (string) Str::uuid(),
            'user_id' => $request->user_id,
            'food_id' => $request->food_id,
            'date' => $request->date,
            'meal_type' => $request->meal_type,
            'serving_size' => $request->serving_size,
            'serving_unit' => $request->serving_unit
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Food log created successfully',
            'data' => $foodLog
        ], 201);
    }

    /**
     * Display the specified food log.
     */
    public function show($log_id)
    {
        $foodLog = UserFoodLog::with('foodItem')->find($log_id);

        if (!$foodLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Food log not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $foodLog
        ]);
    }

    /**
     * Update the specified food log.
     */
    public function update(Request $request, $log_id)
    {
        $foodLog = UserFoodLog::find($log_id);

        if (!$foodLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Food log not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'food_id' => 'string|exists:food_items,food_id',
            'date' => 'date',
            'meal_type' => 'string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'numeric|min:0',
            'serving_unit' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $foodLog->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Food log updated successfully',
            'data' => $foodLog
        ]);
    }

    /**
     * Remove the specified food log.
     */
    public function destroy($log_id)
    {
        $foodLog = UserFoodLog::find($log_id);

        if (!$foodLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Food log not found'
            ], 404);
        }

        $foodLog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Food log deleted successfully'
        ]);
    }
}
