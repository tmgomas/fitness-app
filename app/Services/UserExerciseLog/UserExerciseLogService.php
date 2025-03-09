<?php

namespace App\Services\UserExerciseLog;

use App\Services\UserExerciseLog\Interfaces\UserExerciseLogServiceInterface;
use App\Repositories\UserExerciseLog\Interfaces\UserExerciseLogRepositoryInterface;
use App\Repositories\Exercise\Interfaces\ExerciseRepositoryInterface;
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
                $data['exercise_id'],
                $data['duration_minutes'],
                $data['intensity_level']
            );
            if (isset($data['calories_burned']) && $data['calories_burned'] < 0) {
                $data['calories_burned'] = abs($data['calories_burned']); // ඍණ අගයක් නම්, එය නරපේක්ෂ අගයට පරිවර්තනය කරන්න
            }

            if (isset($data['duration_minutes']) && $data['duration_minutes'] < 0) {
                $data['duration_minutes'] = abs($data['duration_minutes']); // ඍණ අගයක් නම්, එය නරපේක්ෂ අගයට පරිවර්තනය කරන්න
            }
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

            // Recalculate calories if duration, exercise or intensity changed
            if (isset($data['duration_minutes']) || isset($data['exercise_id']) || isset($data['intensity_level'])) {
                $data['calories_burned'] = $this->calculateCaloriesBurned(
                    $data['exercise_id'] ?? $exerciseLog->exercise_id,
                    $data['duration_minutes'] ?? $exerciseLog->duration_minutes,
                    $data['intensity_level'] ?? $exerciseLog->intensity_level
                );
            }
            // සහතික කරන්න calories_burned හා duration_minutes ධන අගයන් ලෙස ගබඩාවන බවට
            if (isset($data['calories_burned']) && $data['calories_burned'] < 0) {
                $data['calories_burned'] = abs($data['calories_burned']); // ඍණ අගයක් නම්, එය නරපේක්ෂ අගයට පරිවර්තනය කරන්න
            }

            if (isset($data['duration_minutes']) && $data['duration_minutes'] < 0) {
                $data['duration_minutes'] = abs($data['duration_minutes']); // ඍණ අගයක් නම්, එය නරපේක්ෂ අගයට පරිවර්තනය කරන්න
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

    public function calculateCaloriesBurned($exerciseId, $durationMinutes, $intensityLevel)
    {
        // ව්‍යායාමය ලබා ගන්න
        $exercise = $this->exerciseRepository->find($exerciseId);

        if (!$exercise) {
            Log::error('Exercise not found: ' . $exerciseId);
            return 0;
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

        // විවේක කාලය සහ යථාර්ථවාදීත්වය සඳහා සුළු අඩු කිරීමක්
        // 600 වෙනුවට 590 වැනි අගයක් ලබා දීමට 1.5% - 2% අතර අඩු කරමු
        $reductionPercentage = mt_rand(15, 20) / 10; // 1.5% - 2.0% අතර අහඹු අගයක්

        // අවසාන කැලරි ගණනය
        $adjustedCalories = $originalCalories * (1 - ($reductionPercentage / 100));

        // උදාහරණයක්:
        // පිහිනීම (medium): 60 * 10 * 1.0 = 600 කැලරි
        // 2% අඩු කළ විට: 600 * 0.98 = 588 කැලරි

        return round($adjustedCalories); // අගය ගොනු කරමු
    }

    protected function getDefaultDateRange(array $dateRange)
    {
        return [
            'start_date' => $dateRange['start_date'] ?? Carbon::now()->subDays(30)->startOfDay(),
            'end_date' => $dateRange['end_date'] ?? Carbon::now()->endOfDay()
        ];
    }
}
