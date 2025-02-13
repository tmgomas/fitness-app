<?php

namespace App\Services\UserExerciseLog;

use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserExerciseLogService implements UserExerciseLogServiceInterface
{
    protected $exerciseLogRepository;

    public function __construct(UserExerciseLogRepositoryInterface $exerciseLogRepository)
    {
        $this->exerciseLogRepository = $exerciseLogRepository;
    }

    public function getAllExerciseLogs(array $filters = [])
    {
        try {
            return $this->exerciseLogRepository->getAllForUser(Auth::id(), $filters);
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

            // Calculate calories burned
            $data['calories_burned'] = $this->calculateCaloriesBurned(
                $data['duration_minutes'],
                $data['intensity_level']
            );

            return $this->exerciseLogRepository->createForUser(Auth::id(), $data);
        } catch (\Exception $e) {
            Log::error('Error storing exercise log: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getExerciseLog($logId)
    {
        try {
            return $this->exerciseLogRepository->findForUser(Auth::id(), $logId);
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

            // Recalculate calories if duration or intensity changed
            if (isset($data['duration_minutes']) || isset($data['intensity_level'])) {
                $data['calories_burned'] = $this->calculateCaloriesBurned(
                    $data['duration_minutes'] ?? $exerciseLog->duration_minutes,
                    $data['intensity_level'] ?? $exerciseLog->intensity_level
                );
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

    public function calculateCaloriesBurned($durationMinutes, $intensityLevel)
    {
        $calorieMultiplier = [
            'low' => 5,
            'medium' => 7,
            'high' => 10
        ][$intensityLevel];

        return $durationMinutes * $calorieMultiplier;
    }

    protected function getDefaultDateRange(array $dateRange)
    {
        return [
            'start_date' => $dateRange['start_date'] ?? Carbon::now()->subDays(30)->startOfDay(),
            'end_date' => $dateRange['end_date'] ?? Carbon::now()->endOfDay()
        ];
    }
}
