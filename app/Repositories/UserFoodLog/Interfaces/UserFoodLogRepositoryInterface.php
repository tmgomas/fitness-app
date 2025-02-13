<?php

namespace App\Repositories\UserFoodLog\Interfaces;

interface UserFoodLogRepositoryInterface
{
    public function getAllForUser($userId);
    public function createForUser($userId, array $data);
    public function findForUser($userId, $foodLogId);
    public function updateForUser($userId, $foodLogId, array $data);
    public function deleteForUser($userId, $foodLogId);
    public function getFoodLogsWithFilters($userId, array $filters);
}
