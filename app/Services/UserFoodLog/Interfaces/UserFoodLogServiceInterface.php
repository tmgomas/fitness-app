<?php

namespace App\Services\UserFoodLog\Interfaces;

interface UserFoodLogServiceInterface
{
    public function getAllFoodLogs(array $filters);
    public function storeFoodLog(array $data);
    public function getFoodLog($foodLogId);
    public function updateFoodLog($foodLogId, array $data);
    public function deleteFoodLog($foodLogId);
    public function getDailyNutritionReport(array $filters);
}
