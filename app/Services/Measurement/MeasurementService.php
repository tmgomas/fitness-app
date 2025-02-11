<?php

namespace App\Services\Measurement;

use App\Services\Measurement\Interfaces\MeasurementServiceInterface;
use App\Repositories\Measurement\Interfaces\MeasurementRepositoryInterface;
use Illuminate\Support\Facades\Auth;

class MeasurementService implements MeasurementServiceInterface
{
    private $measurementRepository;

    public function __construct(MeasurementRepositoryInterface $measurementRepository)
    {
        $this->measurementRepository = $measurementRepository;
    }

    public function getAllMeasurements()
    {
        return $this->measurementRepository->getAllByUserId(Auth::id());
    }

    public function getMeasurement(string $id)
    {
        return $this->measurementRepository->findByIdAndUser($id, Auth::id());
    }

    public function createMeasurement(array $data)
    {
        $data['user_id'] = Auth::id();
        return $this->measurementRepository->create($data);
    }

    public function updateMeasurement(string $id, array $data)
    {
        return $this->measurementRepository->updateByIdAndUser($id, Auth::id(), $data);
    }

    public function deleteMeasurement(string $id)
    {
        return $this->measurementRepository->deleteByIdAndUser($id, Auth::id());
    }
}
