<?php

namespace App\Repositories\HealthData\Interfaces;

interface HealthDataRepositoryInterface
{
    public function getAllForUser($userId);
    public function createForUser($userId, array $data);
    public function findForUser($userId, $healthId);
    public function updateForUser($userId, $healthId, array $data);
    public function deleteForUser($userId, $healthId);
}
