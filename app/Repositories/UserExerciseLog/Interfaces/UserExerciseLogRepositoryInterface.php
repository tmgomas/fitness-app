<?php

namespace App\Repositories\UserExerciseLog\Interfaces;

interface UserExerciseLogRepositoryInterface
{
    public function getAllForUser($userId, array $filters = []);
    public function createForUser($userId, array $data);
    public function findForUser($userId, $logId);
    public function updateForUser($userId, $logId, array $data);
    public function deleteForUser($userId, $logId);
    public function getStatsForUser($userId, array $dateRange);
}
