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
    public function calculateCaloriesBurned($durationMinutes, $intensityLevel);
}
