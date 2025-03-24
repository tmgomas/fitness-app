<?php

namespace App\Services\UserExerciseLog;

use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserExerciseLogService implements UserExerciseLogServiceInterface
{
    protected $exerciseLogRepository;
    protected $exerciseRepository;
    protected $nutritionService;

    public function __construct(
        UserExerciseLogRepositoryInterface $exerciseLogRepository,
        ExerciseRepositoryInterface $exerciseRepository,
        DailyNutritionServiceInterface $nutritionService
    ) {
        $this->exerciseLogRepository = $exerciseLogRepository;
        $this->exerciseRepository = $exerciseRepository;
        $this->nutritionService = $nutritionService;
    }

    public function getAllExerciseLogs(array $filters = [])
    {
        try {
            $logs = $this->exerciseLogRepository->getAllForUser(Auth::id(), $filters);

            // This ensures both standard exercises and custom exercises are loaded
            return $logs->load(['exercise', 'customExercise']);
        } catch (\Exception $e) {
            Log::error('Error fetching exercise logs: ' . $e->getMessage());
            throw $e;
        }
    }
public function storeExerciseLog(array $data)
{
    try {
        // Calculate duration
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        
        // Ensure end time is after start time
        if ($endTime <= $startTime) {
            $endTime = $startTime->copy()->addHour(); // Default to 1 hour if end time is invalid
            $data['end_time'] = $endTime->toDateTimeString();
        }
        
        $data['duration_minutes'] = $endTime->diffInMinutes($startTime);
        
        // Make sure duration is positive
        if ($data['duration_minutes'] <= 0) {
            $data['duration_minutes'] = 60; // Default to 60 minutes if calculation gives 0 or negative
        }

        // Get the user's base recommended calories
        $user = Auth::user();
        $recommendedCalories = $this->nutritionService->getRecommendedCalories($user);
        $baseRecommendedCalories = $recommendedCalories['base_tdee'] ?? 2000;
        
        // Calculate calories burned based on exercise type
        if (isset($data['exercise_id']) && $data['exercise_id']) {
            // Standard exercise
            $caloriesData = $this->calculateCaloriesBurned(
                $data['exercise_id'],
                $data['duration_minutes'],
                $data['intensity_level'],
                $baseRecommendedCalories
            );
            
            // Debug log
            Log::info('Setting calories for standard exercise', [
                'adjusted_calories' => $caloriesData['adjusted_calories'],
                'real_calories' => $caloriesData['real_calories']
            ]);
            
            // Explicitly set both fields
            $data['calories_burned'] = $caloriesData['adjusted_calories'];
            $data['real_calories_burned'] = $caloriesData['real_calories'];
            
        } elseif (isset($data['custom_exercise_id']) && $data['custom_exercise_id']) {
            // Custom exercise
            $caloriesData = $this->calculateCustomExerciseCaloriesBurned(
                $data['custom_exercise_id'],
                $data['duration_minutes'],
                $data['intensity_level'],
                $baseRecommendedCalories
            );
            
            // Debug log
            Log::info('Setting calories for custom exercise', [
                'adjusted_calories' => $caloriesData['adjusted_calories'],
                'real_calories' => $caloriesData['real_calories']
            ]);
            
            // Explicitly set both fields
            $data['calories_burned'] = $caloriesData['adjusted_calories'];
            $data['real_calories_burned'] = $caloriesData['real_calories'];
        }

        // Ensure positive values for duration and calories
        if (!isset($data['calories_burned']) || $data['calories_burned'] < 0) {
            $data['calories_burned'] = isset($data['calories_burned']) ? abs($data['calories_burned']) : 0;
        }

        if (!isset($data['real_calories_burned']) || $data['real_calories_burned'] < 0) {
            $data['real_calories_burned'] = isset($data['real_calories_burned']) ? abs($data['real_calories_burned']) : 0;
        }

        // Log the final data being sent to the repository
        Log::info('Final data being sent to repository', [
            'user_id' => Auth::id(),
            'exercise_id' => $data['exercise_id'] ?? null,
            'custom_exercise_id' => $data['custom_exercise_id'] ?? null,
            'duration_minutes' => $data['duration_minutes'],
            'calories_burned' => $data['calories_burned'] ?? 0,
            'real_calories_burned' => $data['real_calories_burned'] ?? 0
        ]);

        // Create the exercise log
        $result = $this->exerciseLogRepository->createForUser(Auth::id(), $data);
        
        // Log what was actually created
        Log::info('Exercise log created', [
            'log_id' => $result->id ?? 'unknown',
            'calories_burned' => $result->calories_burned ?? 'not set',
            'real_calories_burned' => $result->real_calories_burned ?? 'not set'
        ]);
        
        return $result;
    } catch (\Exception $e) {
        Log::error('Error storing exercise log: ' . $e->getMessage());
        throw $e;
    }
}

    // අලුත් ක්‍රමයක් custom exercises සඳහා
private function calculateCustomExerciseCaloriesBurned($customExerciseId, $durationMinutes, $intensityLevel, $baseRecommendedCalories = 2000)
{
    try {
        // Ensure duration is positive
        $durationMinutes = abs($durationMinutes);
        
        // Get custom exercise
        $customExercise = app(CustomExerciseServiceInterface::class)->getCustomExercise($customExerciseId);

        if (!$customExercise) {
            Log::error('Custom exercise not found: ' . $customExerciseId);
            return ['adjusted_calories' => 0, 'real_calories' => 0];
        }

        // Get base calories per minute
        $baseCaloriesPerMinute = $customExercise->calories_per_minute;

        // Apply intensity multiplier
        $intensityMultipliers = [
            'low' => 0.8,
            'medium' => 1.0,
            'high' => 1.3
        ];

        $intensityMultiplier = $intensityMultipliers[$intensityLevel] ?? 1.0;

        // Basic calculation
        $originalCalories = $durationMinutes * $baseCaloriesPerMinute * $intensityMultiplier;

        // Store real calories (without any reduction)
        $realCalories = round($originalCalories);

        // Remove the reduction percentage - use original calories directly
        $adjustedCalories = $originalCalories;

        // Calculate calories burned at rest during this exercise duration
        $caloriesAtRest = ($baseRecommendedCalories / 24) * ($durationMinutes / 60);
        
        // Subtract calories that would have been burned at rest
        $finalAdjustedCalories = max(0, $adjustedCalories - $caloriesAtRest);
        
        // Log calculation details
        Log::info('Custom Exercise Calories Calculation', [
            'custom_exercise_id' => $customExerciseId,
            'duration_minutes' => $durationMinutes,
            'intensity_level' => $intensityLevel,
            'base_calories_per_minute' => $baseCaloriesPerMinute,
            'intensity_multiplier' => $intensityMultiplier,
            'original_calories' => $originalCalories,
            'base_recommended_calories' => $baseRecommendedCalories,
            'calories_at_rest' => $caloriesAtRest,
            'final_adjusted_calories' => $finalAdjustedCalories,
            'real_calories' => $realCalories
        ]);

        // Make sure to return values as integers to avoid any floating point issues
        return [
            'adjusted_calories' => (int)round($finalAdjustedCalories),
            'real_calories' => (int)round($realCalories)
        ];
    } catch (\Exception $e) {
        Log::error('Error calculating custom exercise calories: ' . $e->getMessage());
        return ['adjusted_calories' => 0, 'real_calories' => 0];
    }
}
    public function getExerciseLog($logId)
    {
        try {
            $log = $this->exerciseLogRepository->findForUser(Auth::id(), $logId);

            // Make sure to load both relationships
            return $log->load(['exercise', 'customExercise']);
        } catch (\Exception $e) {
            Log::error('Error fetching exercise log: ' . $e->getMessage());
            throw $e;
        }
    }
public function updateExerciseLog($logId, array $data)
{
    try {
        $exerciseLog = $this->getExerciseLog($logId);

        // Get the user's base recommended calories
        $user = Auth::user();
        $recommendedCalories = $this->nutritionService->getRecommendedCalories($user);
        $baseRecommendedCalories = $recommendedCalories['base_tdee'] ?? 2000;

        // Recalculate duration if times changed
        if (isset($data['start_time']) || isset($data['end_time'])) {
            $startTime = Carbon::parse($data['start_time'] ?? $exerciseLog->start_time);
            $endTime = Carbon::parse($data['end_time'] ?? $exerciseLog->end_time);
            
            // Ensure end time is after start time
            if ($endTime <= $startTime) {
                $endTime = $startTime->copy()->addHour(); // Default to 1 hour if end time is invalid
                $data['end_time'] = $endTime->toDateTimeString();
            }
            
            $data['duration_minutes'] = $endTime->diffInMinutes($startTime);
            
            // Make sure duration is positive
            if ($data['duration_minutes'] <= 0) {
                $data['duration_minutes'] = 60; // Default to 60 minutes if calculation gives 0 or negative
            }
        }

        // Recalculate calories for standard exercises
        if ((isset($data['exercise_id']) && $data['exercise_id']) ||
            ($exerciseLog->exercise_id && !isset($data['exercise_id']))
        ) {
            // Handle standard exercise
            $exerciseId = $data['exercise_id'] ?? $exerciseLog->exercise_id;
            if (isset($data['duration_minutes']) || isset($data['exercise_id']) || isset($data['intensity_level'])) {
                $caloriesData = $this->calculateCaloriesBurned(
                    $exerciseId,
                    $data['duration_minutes'] ?? $exerciseLog->duration_minutes,
                    $data['intensity_level'] ?? $exerciseLog->intensity_level,
                    $baseRecommendedCalories
                );
                
                // Debug log
                Log::info('Updating calories for standard exercise', [
                    'adjusted_calories' => $caloriesData['adjusted_calories'],
                    'real_calories' => $caloriesData['real_calories']
                ]);
                
                // Explicitly set both fields
                $data['calories_burned'] = $caloriesData['adjusted_calories'];
                $data['real_calories_burned'] = $caloriesData['real_calories'];
            }
        }
        // Recalculate calories for custom exercises
        elseif ((isset($data['custom_exercise_id']) && $data['custom_exercise_id']) ||
            ($exerciseLog->custom_exercise_id && !isset($data['custom_exercise_id']))
        ) {
            // Handle custom exercise
            $customExerciseId = $data['custom_exercise_id'] ?? $exerciseLog->custom_exercise_id;
            if (isset($data['duration_minutes']) || isset($data['custom_exercise_id']) || isset($data['intensity_level'])) {
                $caloriesData = $this->calculateCustomExerciseCaloriesBurned(
                    $customExerciseId,
                    $data['duration_minutes'] ?? $exerciseLog->duration_minutes,
                    $data['intensity_level'] ?? $exerciseLog->intensity_level,
                    $baseRecommendedCalories
                );
                
                // Debug log
                Log::info('Updating calories for custom exercise', [
                    'adjusted_calories' => $caloriesData['adjusted_calories'],
                    'real_calories' => $caloriesData['real_calories']
                ]);
                
                // Explicitly set both fields
                $data['calories_burned'] = $caloriesData['adjusted_calories'];
                $data['real_calories_burned'] = $caloriesData['real_calories'];
            }
        }

        // Ensure positive values and set defaults if missing
        if (!isset($data['calories_burned']) || $data['calories_burned'] < 0) {
            $data['calories_burned'] = isset($data['calories_burned']) ? abs($data['calories_burned']) : 0;
        }

        if (!isset($data['real_calories_burned']) || $data['real_calories_burned'] < 0) {
            $data['real_calories_burned'] = isset($data['real_calories_burned']) ? abs($data['real_calories_burned']) : 0;
        }

        if (!isset($data['duration_minutes']) || $data['duration_minutes'] < 0) {
            $data['duration_minutes'] = isset($data['duration_minutes']) ? abs($data['duration_minutes']) : 60;
        }

        // Log the final data being sent to the repository
        Log::info('Final data being sent to repository for update', [
            'user_id' => Auth::id(),
            'log_id' => $logId,
            'exercise_id' => $data['exercise_id'] ?? $exerciseLog->exercise_id ?? null,
            'custom_exercise_id' => $data['custom_exercise_id'] ?? $exerciseLog->custom_exercise_id ?? null,
            'duration_minutes' => $data['duration_minutes'] ?? $exerciseLog->duration_minutes,
            'calories_burned' => $data['calories_burned'] ?? 0,
            'real_calories_burned' => $data['real_calories_burned'] ?? 0
        ]);

        // Update the exercise log
        $result = $this->exerciseLogRepository->updateForUser(Auth::id(), $logId, $data);
        
        // Log what was actually updated
        Log::info('Exercise log updated', [
            'log_id' => $result->id ?? 'unknown',
            'calories_burned' => $result->calories_burned ?? 'not set',
            'real_calories_burned' => $result->real_calories_burned ?? 'not set'
        ]);
        
        return $result;
    } catch (\Exception $e) {
        Log::error('Error updating exercise log: ' . $e->getMessage());
        throw $e;
    }
}
    public function deleteExerciseLog($logId)
    {
        try {
            return $this->exerciseLogRepository->deleteForUser(Auth::id(), $logId);
        } catch (\Exception $e) {
            Log::error('Error deleting exercise log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getExerciseStats(array $dateRange = [])
    {
        try {
            $dateRange = $this->getDefaultDateRange($dateRange);
            $rawStats = $this->exerciseLogRepository->getStatsForUser(Auth::id(), $dateRange);

            return [
                'total_workouts' => $rawStats['stats']->total_workouts,
                'total_duration' => round($rawStats['stats']->total_duration, 2),
                'total_calories' => round($rawStats['stats']->total_calories, 2),
                'total_real_calories' => round($rawStats['stats']->total_real_calories ?? $rawStats['stats']->total_calories, 2),
                'average_heart_rate' => round($rawStats['stats']->average_heart_rate, 2),
                'active_days' => $rawStats['stats']->active_days,
                'most_common_exercise' => $rawStats['most_common_exercise'] ? [
                    'name' => $rawStats['most_common_exercise']->exercise->name,
                    'count' => $rawStats['most_common_exercise']->count
                ] : null
            ];
        } catch (\Exception $e) {
            Log::error('Error generating exercise stats: ' . $e->getMessage());
            throw $e;
        }
    }
public function calculateCaloriesBurned($exerciseId, $durationMinutes, $intensityLevel, $baseRecommendedCalories = 2000)
{
    // Ensure duration is positive
    $durationMinutes = abs($durationMinutes);
    
    // ව්‍යායාමය ලබා ගන්න
    $exercise = $this->exerciseRepository->find($exerciseId);

    if (!$exercise) {
        Log::error('Exercise not found: ' . $exerciseId);
        return ['adjusted_calories' => 0, 'real_calories' => 0];
    }

    // ව්‍යායාමයේ විනාඩියකට කැලරි අගය භාවිතා කරන්න
    $baseCaloriesPerMinute = $exercise->calories_per_minute;

    // තීව්‍රතාවය ගුණකය - මුල් ගුණක භාවිතා කරමු
    $intensityMultipliers = [
        'low' => 0.8,
        'medium' => 1.0,
        'high' => 1.3
    ];

    $intensityMultiplier = $intensityMultipliers[$intensityLevel] ?? 1.0;

    // මූලික ගණනය - මුල් විදිහටම
    $originalCalories = $durationMinutes * $baseCaloriesPerMinute * $intensityMultiplier;

    // Store real calories (without any reduction)
    $realCalories = round($originalCalories);

    // Remove the reduction percentage - we're now using the original calories directly
    $adjustedCalories = $originalCalories;

    // Calculate calories burned at rest during this exercise duration
    $caloriesAtRest = ($baseRecommendedCalories / 24) * ($durationMinutes / 60);
    
    // Subtract calories that would have been burned at rest
    $finalAdjustedCalories = max(0, $adjustedCalories - $caloriesAtRest);
    
    // Log calculation details
    Log::info('Standard Exercise Calories Calculation', [
        'exercise_id' => $exerciseId,
        'duration_minutes' => $durationMinutes,
        'intensity_level' => $intensityLevel,
        'base_calories_per_minute' => $baseCaloriesPerMinute,
        'intensity_multiplier' => $intensityMultiplier,
        'original_calories' => $originalCalories,
        'base_recommended_calories' => $baseRecommendedCalories,
        'calories_at_rest' => $caloriesAtRest,
        'final_adjusted_calories' => $finalAdjustedCalories,
        'real_calories' => $realCalories
    ]);

    // Make sure to return values as integers to avoid any floating point issues
    return [
        'adjusted_calories' => (int)round($finalAdjustedCalories),
        'real_calories' => (int)round($realCalories)
    ];
}

    protected function getDefaultDateRange(array $dateRange)
    {
        return [
            'start_date' => $dateRange['start_date'] ?? Carbon::now()->subDays(30)->startOfDay(),
            'end_date' => $dateRange['end_date'] ?? Carbon::now()->endOfDay()
        ];
    }
}