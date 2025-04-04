<?php

namespace App\Services\UserExerciseLog;

use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface;
use App\Services\Nutrition\Interfaces\DailyNutritionServiceInterface;
use App\Models\User; // Add this line to import the User model
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserExerciseLogService implements UserExerciseLogServiceInterface
{
    protected $exerciseLogRepository;
    protected $exerciseRepository;
    protected $nutritionService; // Added this

    public function __construct(
        UserExerciseLogRepositoryInterface $exerciseLogRepository,
        ExerciseRepositoryInterface $exerciseRepository,
        DailyNutritionServiceInterface $nutritionService // Added this
    ) {
        $this->exerciseLogRepository = $exerciseLogRepository;
        $this->exerciseRepository = $exerciseRepository;
        $this->nutritionService = $nutritionService; // Added this
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
        $data['duration_minutes'] = $endTime->diffInMinutes($startTime);

        // Calculate calories burned based on exercise type only if not provided by frontend
        if (!isset($data['calories_burned']) || !isset($data['real_calories_burned'])) {
            if (isset($data['exercise_id']) && $data['exercise_id']) {
                // Standard exercise
                $caloriesData = $this->calculateCaloriesBurned(
                    $data['exercise_id'],
                    $data['duration_minutes'],
                    $data['intensity_level']
                );
                // Only set if not already set
                if (!isset($data['calories_burned'])) {
                    $data['calories_burned'] = $caloriesData['adjusted_calories'];
                }
                if (!isset($data['real_calories_burned'])) {
                    $data['real_calories_burned'] = $caloriesData['real_calories'];
                }
            } elseif (isset($data['custom_exercise_id']) && $data['custom_exercise_id']) {
                // Custom exercise
                $caloriesData = $this->calculateCustomExerciseCaloriesBurned(
                    $data['custom_exercise_id'],
                    $data['duration_minutes'],
                    $data['intensity_level']
                );
                // Only set if not already set
                if (!isset($data['calories_burned'])) {
                    $data['calories_burned'] = $caloriesData['adjusted_calories'];
                }
                if (!isset($data['real_calories_burned'])) {
                    $data['real_calories_burned'] = $caloriesData['real_calories'];
                }
            }
        }

        // Ensure positive values for duration and calories
        if (isset($data['calories_burned']) && $data['calories_burned'] < 0) {
            $data['calories_burned'] = abs($data['calories_burned']);
        }

        if (isset($data['real_calories_burned']) && $data['real_calories_burned'] < 0) {
            $data['real_calories_burned'] = abs($data['real_calories_burned']);
        }

        if (isset($data['duration_minutes']) && $data['duration_minutes'] < 0) {
            $data['duration_minutes'] = abs($data['duration_minutes']);
        }

        return $this->exerciseLogRepository->createForUser(Auth::id(), $data);
    } catch (\Exception $e) {
        Log::error('Error storing exercise log: ' . $e->getMessage());
        throw $e;
    }
}

private function calculateCustomExerciseCaloriesBurned($customExerciseId, $durationMinutes, $intensityLevel)
{
    try {
        // Get custom exercise
        $customExercise = app(CustomExerciseServiceInterface::class)->getCustomExercise($customExerciseId);

        if (!$customExercise) {
            Log::error('Custom exercise not found: ' . $customExerciseId);
            return ['adjusted_calories' => 0, 'real_calories' => 0];
        }

        // Get user's recommended calories
        $userId = Auth::id();
        $user = User::find($userId);
        
        // Get daily recommended calories from nutrition service
        $recommendedData = $this->nutritionService->getRecommendedCalories($user);
        $baseRecommendedCalories = $recommendedData['total_calories'] ?? 2000;
        
        // Calculate hourly calorie burn rate (calories per hour at rest)
        $hourlyBaseRate = $baseRecommendedCalories / 24;
        
        // Convert to per minute rate
        $baseCaloriesPerMinute = $hourlyBaseRate / 60;
        
        // Get base calories per minute from custom exercise
        $exerciseCaloriesPerMinute = $customExercise->calories_per_minute;
        
        // For real_calories_burned, use the original exercise calories without subtracting base rate
        $fullExerciseCaloriesPerMinute = $exerciseCaloriesPerMinute;
        
        // Final calories per minute is the exercise rate MINUS the base rate
        // (since we only want the ADDITIONAL calories burned by exercising)
        $netCaloriesPerMinute = $exerciseCaloriesPerMinute - $baseCaloriesPerMinute;
        
        // Make sure it's not negative
        if ($netCaloriesPerMinute < 0) {
            $netCaloriesPerMinute = 0;
        }

        // Apply intensity multiplier
        $intensityMultipliers = [
            'low' => 0.8,
            'medium' => 1.0,
            'high' => 1.3
        ];

        $intensityMultiplier = $intensityMultipliers[$intensityLevel] ?? 1.0;

        // Calculate adjusted calories (net calories after subtracting base rate)
        $adjustedCalories = $durationMinutes * $netCaloriesPerMinute * $intensityMultiplier;
        
        // Calculate real calories (original calories without subtracting base rate)
        $realCalories = $durationMinutes * $fullExerciseCaloriesPerMinute * $intensityMultiplier;

        return [
            'adjusted_calories' => round($adjustedCalories),
            'real_calories' => round($realCalories)
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

            // Recalculate duration if times changed
            if (isset($data['start_time']) || isset($data['end_time'])) {
                $startTime = Carbon::parse($data['start_time'] ?? $exerciseLog->start_time);
                $endTime = Carbon::parse($data['end_time'] ?? $exerciseLog->end_time);
                $data['duration_minutes'] = $endTime->diffInMinutes($startTime);
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
                        $data['intensity_level'] ?? $exerciseLog->intensity_level
                    );
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
                        $data['intensity_level'] ?? $exerciseLog->intensity_level
                    );
                    $data['calories_burned'] = $caloriesData['adjusted_calories'];
                    $data['real_calories_burned'] = $caloriesData['real_calories'];
                }
            }

            // Ensure positive values
            if (isset($data['calories_burned']) && $data['calories_burned'] < 0) {
                $data['calories_burned'] = abs($data['calories_burned']);
            }

            if (isset($data['real_calories_burned']) && $data['real_calories_burned'] < 0) {
                $data['real_calories_burned'] = abs($data['real_calories_burned']);
            }

            if (isset($data['duration_minutes']) && $data['duration_minutes'] < 0) {
                $data['duration_minutes'] = abs($data['duration_minutes']);
            }

            return $this->exerciseLogRepository->updateForUser(Auth::id(), $logId, $data);
        } catch (\Exception $e) {
            Log::error('Error updating exercise log: ' . $e->getMessage());
            throw $e;
        }
    }
public function calculateCaloriesBurned($exerciseId, $durationMinutes, $intensityLevel)
{
    try {
        // ව්‍යායාමය ලබා ගන්න
        $exercise = $this->exerciseRepository->find($exerciseId);

        if (!$exercise) {
            Log::error('Exercise not found: ' . $exerciseId);
            return ['adjusted_calories' => 0, 'real_calories' => 0];
        }

        // Get user's recommended calories
        $userId = Auth::id();
        $user = User::find($userId);
        
        // Get daily recommended calories from nutrition service
        $recommendedData = $this->nutritionService->getRecommendedCalories($user);
        $baseRecommendedCalories = $recommendedData['total_calories'] ?? 2000;
        
        // Calculate hourly calorie burn rate (calories per hour at rest)
        $hourlyBaseRate = $baseRecommendedCalories / 24;
        
        // Convert to per minute rate
        $baseCaloriesPerMinute = $hourlyBaseRate / 60;
        
        // ව්‍යායාමයේ විනාඩියකට කැලරි අගය භාවිතා කරන්න
        $exerciseCaloriesPerMinute = $exercise->calories_per_minute;
        
        // For real_calories_burned, use the original exercise calories without subtracting base rate
        $fullExerciseCaloriesPerMinute = $exerciseCaloriesPerMinute;
        
        // Final calories per minute is the exercise rate MINUS the base rate
        // (since we only want the ADDITIONAL calories burned by exercising)
        $netCaloriesPerMinute = $exerciseCaloriesPerMinute - $baseCaloriesPerMinute;
        
        // Make sure it's not negative
        if ($netCaloriesPerMinute < 0) {
            $netCaloriesPerMinute = 0;
        }

        // තීව්‍රතාවය ගුණකය - මුල් ගුණක භාවිතා කරමු
        $intensityMultipliers = [
            'low' => 0.8,
            'medium' => 1.0,
            'high' => 1.3
        ];

        $intensityMultiplier = $intensityMultipliers[$intensityLevel] ?? 1.0;

        // Calculate adjusted calories (net calories after subtracting base rate)
        $adjustedCalories = $durationMinutes * $netCaloriesPerMinute * $intensityMultiplier;
        
        // Calculate real calories (original calories without subtracting base rate)
        $realCalories = $durationMinutes * $fullExerciseCaloriesPerMinute * $intensityMultiplier;

        return [
            'adjusted_calories' => round($adjustedCalories),
            'real_calories' => round($realCalories)
        ];
    } catch (\Exception $e) {
        Log::error('Error calculating exercise calories: ' . $e->getMessage());
        return ['adjusted_calories' => 0, 'real_calories' => 0];
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

    protected function getDefaultDateRange(array $dateRange)
    {
        return [
            'start_date' => $dateRange['start_date'] ?? Carbon::now()->subDays(30)->startOfDay(),
            'end_date' => $dateRange['end_date'] ?? Carbon::now()->endOfDay()
        ];
    }
}