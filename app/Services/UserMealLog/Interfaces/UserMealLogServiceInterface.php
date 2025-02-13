<?php

namespace App\Services\UserMealLog\Interfaces;

interface UserMealLogServiceInterface
{
    public function getAllMealLogs(array $filters = []);
    public function storeMealLog(array $data);
    public function getMealLog($mealLogId);
    public function updateMealLog($mealLogId, array $data);
    public function deleteMealLog($mealLogId);
}
