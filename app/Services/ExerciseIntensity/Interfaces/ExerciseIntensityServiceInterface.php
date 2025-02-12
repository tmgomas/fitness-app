<?php

namespace App\Services\ExerciseIntensity\Interfaces;

interface ExerciseIntensityServiceInterface
{
    public function getAllIntensities();
    public function getPaginatedIntensities(int $perPage = 10);
    public function findIntensity(string $id);
    public function createIntensity(array $data);
    public function updateIntensity(string $id, array $data);
    public function deleteIntensity(string $id);
}
