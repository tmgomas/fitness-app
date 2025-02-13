<?php

namespace App\Repositories\UserMealLog\Interfaces;

interface UserMealLogRepositoryInterface
{
    public function getAllForUser($userId);
    public function createForUser($userId, array $data);
    public function findForUser($userId, $mealLogId);
    public function updateForUser($userId, $mealLogId, array $data);
    public function deleteForUser($userId, $mealLogId);
    public function getMealLogsWithFilters($userId, array $filters);
}
