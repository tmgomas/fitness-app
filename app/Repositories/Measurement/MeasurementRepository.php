<?php

namespace App\Repositories\Measurement;

use App\Models\UserMeasurement;
use App\Repositories\Measurement\Interfaces\MeasurementRepositoryInterface;

class MeasurementRepository implements MeasurementRepositoryInterface
{
    protected $model;

    public function __construct(UserMeasurement $model)
    {
        $this->model = $model;
    }

    public function getAllByUserId(int $userId)
    {
        return $this->model
            ->where('user_id', $userId)
            ->orderBy('recorded_at', 'desc')
            ->get();
    }

    public function findByIdAndUser(string $id, int $userId)
    {
        return $this->model
            ->where('measurement_id', $id)
            ->where('user_id', $userId)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function updateByIdAndUser(string $id, int $userId, array $data)
    {
        $measurement = $this->findByIdAndUser($id, $userId);
        $measurement->update($data);
        return $measurement;
    }

    public function deleteByIdAndUser(string $id, int $userId)
    {
        $measurement = $this->findByIdAndUser($id, $userId);
        return $measurement->delete();
    }
}