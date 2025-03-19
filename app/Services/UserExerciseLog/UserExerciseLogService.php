<?php

namespace App\Services\UserExerciseLog;

use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
use App\Services\CustomExercise\Interfaces\CustomExerciseServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserExerciseLogService implements UserExerciseLogServiceInterface
{
    protected $exerciseLogRepository;
    protected $exerciseRepository;

    public function __construct(
        UserExerciseLogRepositoryInterface $exerciseLogRepository,
        ExerciseRepositoryInterface $exerciseRepository
    ) {
        $this->exerciseLogRepository = $exerciseLogRepository;
        $this->exerciseRepository = $exerciseRepository;
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

            // Calculate calories burned based on exercise type
            if (isset($data['exercise_id']) && $data['exercise_id']) {
                // Standard exercise
                $caloriesData = $this->calculateCaloriesBurned(
                    $data['exercise_id'],
                    $data['duration_minutes'],
                    $data['intensity_level']
                );
                $data['calories_burned'] = $caloriesData['adjusted_calories'];
                $data['real_calories_burned'] = $caloriesData['real_calories'];
            } elseif (isset($data['custom_exercise_id']) && $data['custom_exercise_id']) {
                // Custom exercise
                $caloriesData = $this->calculateCustomExerciseCaloriesBurned(
                    $data['custom_exercise_id'],
                    $data['duration_minutes'],
                    $data['intensity_level']
                );
                $data['calories_burned'] = $caloriesData['adjusted_calories'];
                $data['real_calories_burned'] = $caloriesData['real_calories'];
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

    // අලුත් ක්‍රමයක් custom exercises සඳහා
    private function calculateCustomExerciseCaloriesBurned($customExerciseId, $durationMinutes, $intensityLevel)
    {
        try {
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

            // Store real calories (without random reduction)
            $realCalories = round($originalCalories);

            // Small reduction for realism (1.5% - 2.0%)
            $reductionPercentage = mt_rand(15, 20) / 10;
            $adjustedCalories = $originalCalories * (1 - ($reductionPercentage / 100));

            return [
                'adjusted_calories' => round($adjustedCalories),
                'real_calories' => $realCalories
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

    public function calculateCaloriesBurned($exerciseId, $durationMinutes, $intensityLevel)
    {
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

        // Store real calories (without random reduction)
        $realCalories = round($originalCalories);

        // විවේක කාලය සහ යථාර්ථවාදීත්වය සඳහා සුළු අඩු කිරීමක්
        // 600 වෙනුවට 590 වැනි අගයක් ලබා දීමට 1.5% - 2% අතර අඩු කරමු
        $reductionPercentage = mt_rand(15, 20) / 10; // 1.5% - 2.0% අතර අහඹු අගයක්

        // අවසාන කැලරි ගණනය
        $adjustedCalories = $originalCalories * (1 - ($reductionPercentage / 100));

        return [
            'adjusted_calories' => round($adjustedCalories),
            'real_calories' => $realCalories
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
