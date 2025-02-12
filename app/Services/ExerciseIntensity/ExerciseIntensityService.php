<?php

// app/Services/ExerciseIntensity/ExerciseIntensityService.php

namespace App\Services\ExerciseIntensity;

use App\Repositories\ExerciseIntensity\Interfaces\ExerciseIntensityRepositoryInterface;
use App\Services\ExerciseIntensity\Interfaces\ExerciseIntensityServiceInterface;

class ExerciseIntensityService implements ExerciseIntensityServiceInterface
{
    protected $repository;

    public function __construct(ExerciseIntensityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllIntensities()
    {
        return $this->repository->all();
    }

    public function getPaginatedIntensities(int $perPage = 10)
    {
        return $this->repository->paginate($perPage);
    }

    public function findIntensity(string $id)
    {
        return $this->repository->find($id);
    }

    public function createIntensity(array $data)
    {
        return $this->repository->create($data);
    }

    public function updateIntensity(string $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function deleteIntensity(string $id)
    {
        return $this->repository->delete($id);
    }
}
