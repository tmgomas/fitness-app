<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMealLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserMealLogController extends Controller
{
    /**
     * Display a listing of the meal logs.
     */
    // MealLogController
    public function index(Request $request)
    {
        try {
            $query = UserMealLog::with([
                'meal',
                'meal.nutritionFacts.nutritionType',  // Load meal nutrition facts with types
                'meal.foods.foodItem.foodNutrition.nutritionType'  // Load food nutrition also
            ])->where('user_id', Auth::id());

            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('date', [$startDate, $endDate]);
            }

            $mealLogs = $query->get();

            // Debug logging
            Log::info('Meal Logs Query:', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'count' => $mealLogs->count(),
                'meal_nutrition_count' => $mealLogs->first()?->meal?->nutritionFacts?->count() ?? 0,
                'foods_nutrition_count' => $mealLogs->first()?->meal?->foods?->first()?->foodItem?->foodNutrition?->count() ?? 0
            ]);

            return response()->json(['data' => $mealLogs]);
        } catch (\Exception $e) {
            Log::error('Error in meal logs index: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
