<?php

namespace App\Services\UserExerciseLog\Interfaces;

interface UserExerciseLogServiceInterface
{
    public function getAllExerciseLogs(array $filters = []);
    public function storeExerciseLog(array $data);
    public function getExerciseLog($logId);
    public function updateExerciseLog($logId, array $data);
    public function deleteExerciseLog($logId);
    public function getExerciseStats(array $dateRange = []);

    /**
     * Calculate calories burned during an exercise
     * 
     * @param string $exerciseId The ID of the exercise
     * @param float $durationMinutes Duration of the exercise in minutes
     * @param string $intensityLevel Intensity level of the exercise (low, medium, high)
     * @return float The calories burned
     */
    public function calculateCaloriesBurned($exerciseId, $durationMinutes, $intensityLevel);
}
