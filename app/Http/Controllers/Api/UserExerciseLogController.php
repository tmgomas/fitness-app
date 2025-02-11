<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserExerciseLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class UserExerciseLogController extends Controller
{
    /**
     * Display a listing of exercise logs.
     */
    public function index(Request $request)
    {
        try {
            $query = UserExerciseLog::with([
                'exercise',
                'exercise.category'
            ])->where('user_id', Auth::id());

            if ($request->has('start_date') && $request->has('end_date')) {
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                $query->whereBetween('start_time', [$startDate, $endDate]);
            }

            if ($request->has('exercise_id')) {
                $query->where('exercise_id', $request->exercise_id);
            }

            $exerciseLogs = $query->get();

            // Debug logging
            Log::info('Exercise Logs Query:', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'exercise_id' => $request->exercise_id,
                'count' => $exerciseLogs->count()
            ]);

            return response()->json(['data' => $exerciseLogs]);
        } catch (\Exception $e) {
            Log::error('Error in exercise logs index: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a new exercise log.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|string|exists:users,id',
            'exercise_id' => 'required|string|exists:exercises,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'distance' => 'nullable|numeric|min:0',
            'distance_unit' => 'required_with:distance|in:km,mi',
            'avg_heart_rate' => 'nullable|numeric|min:0|max:250',
            'intensity_level' => 'required|in:low,medium,high',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Calculate duration and calories
        $startTime = Carbon::parse($request->start_time);
        $endTime = Carbon::parse($request->end_time);
        $durationMinutes = $endTime->diffInMinutes($startTime);

        // Basic calorie calculation (you can make this more sophisticated)
        $calorieMultiplier = [
            'low' => 5,
            'medium' => 7,
            'high' => 10
        ][$request->intensity_level];

        $caloriesBurned = $durationMinutes * $calorieMultiplier;

        $exerciseLog = UserExerciseLog::create([
            'user_id' => $request->user_id,
            'exercise_id' => $request->exercise_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $durationMinutes,
            'distance' => $request->distance,
            'distance_unit' => $request->distance_unit,
            'calories_burned' => $caloriesBurned,
            'avg_heart_rate' => $request->avg_heart_rate,
            'intensity_level' => $request->intensity_level,
            'notes' => $request->notes
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Exercise log created successfully',
            'data' => $exerciseLog
        ], 201);
    }

    /**
     * Display the specified exercise log.
     */
    public function show($log_id)
    {
        $exerciseLog = UserExerciseLog::with(['exercise', 'exercise.category'])->find($log_id);

        if (!$exerciseLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exercise log not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $exerciseLog
        ]);
    }

    /**
     * Update the specified exercise log.
     */
    public function update(Request $request, $log_id)
    {
        $exerciseLog = UserExerciseLog::find($log_id);

        if (!$exerciseLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exercise log not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'exercise_id' => 'string|exists:exercises,id',
            'start_time' => 'date',
            'end_time' => 'date|after:start_time',
            'distance' => 'nullable|numeric|min:0',
            'distance_unit' => 'required_with:distance|in:km,mi',
            'avg_heart_rate' => 'nullable|numeric|min:0|max:250',
            'intensity_level' => 'in:low,medium,high',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        // Recalculate duration and calories if time changed
        if ($request->has('start_time') || $request->has('end_time')) {
            $startTime = Carbon::parse($request->start_time ?? $exerciseLog->start_time);
            $endTime = Carbon::parse($request->end_time ?? $exerciseLog->end_time);
            $request->merge(['duration_minutes' => $endTime->diffInMinutes($startTime)]);

            // Update calories if intensity changed or duration changed
            if ($request->has('intensity_level') || $request->has('start_time') || $request->has('end_time')) {
                $calorieMultiplier = [
                    'low' => 5,
                    'medium' => 7,
                    'high' => 10
                ][$request->intensity_level ?? $exerciseLog->intensity_level];

                $request->merge(['calories_burned' => $request->duration_minutes * $calorieMultiplier]);
            }
        }

        $exerciseLog->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Exercise log updated successfully',
            'data' => $exerciseLog
        ]);
    }

    /**
     * Remove the specified exercise log.
     */
    public function destroy($log_id)
    {
        $exerciseLog = UserExerciseLog::find($log_id);

        if (!$exerciseLog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exercise log not found'
            ], 404);
        }

        $exerciseLog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Exercise log deleted successfully'
        ]);
    }

    /**
     * Get exercise statistics for the user.
     */
    public function getStats(Request $request)
    {
        try {
            $userId = Auth::id();
            $startDate = $request->get('start_date')
                ? Carbon::parse($request->start_date)->startOfDay()
                : Carbon::now()->subDays(30)->startOfDay();
            $endDate = $request->get('end_date')
                ? Carbon::parse($request->end_date)->endOfDay()
                : Carbon::now()->endOfDay();

            $stats = UserExerciseLog::where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->selectRaw('
                    COUNT(*) as total_workouts,
                    SUM(duration_minutes) as total_duration,
                    SUM(calories_burned) as total_calories,
                    AVG(avg_heart_rate) as average_heart_rate,
                    COUNT(DISTINCT DATE(start_time)) as active_days
                ')
                ->first();

            // Get most common exercise
            $mostCommonExercise = UserExerciseLog::where('user_id', $userId)
                ->whereBetween('start_time', [$startDate, $endDate])
                ->with('exercise')
                ->select('exercise_id', DB::raw('count(*) as count'))
                ->groupBy('exercise_id')
                ->orderByDesc('count')
                ->first();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_workouts' => $stats->total_workouts,
                    'total_duration' => round($stats->total_duration, 2),
                    'total_calories' => round($stats->total_calories, 2),
                    'average_heart_rate' => round($stats->average_heart_rate, 2),
                    'active_days' => $stats->active_days,
                    'most_common_exercise' => $mostCommonExercise ? [
                        'name' => $mostCommonExercise->exercise->name,
                        'count' => $mostCommonExercise->count
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error in exercise stats: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
