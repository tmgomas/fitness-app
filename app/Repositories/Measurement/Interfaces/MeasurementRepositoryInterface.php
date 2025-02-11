<?php

namespace App\Repositories\Measurement\Interfaces;

interface MeasurementRepositoryInterface
{
    public function getAllByUserId(int $userId);
    public function findByIdAndUser(string $id, int $userId);
    public function create(array $data);
    public function updateByIdAndUser(string $id, int $userId, array $data);
    public function deleteByIdAndUser(string $id, int $userId);
}
