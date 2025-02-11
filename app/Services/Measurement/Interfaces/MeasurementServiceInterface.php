<?php

namespace App\Services\Measurement\Interfaces;

interface MeasurementServiceInterface
{
    public function getAllMeasurements();
    public function getMeasurement(string $id);
    public function createMeasurement(array $data);
    public function updateMeasurement(string $id, array $data);
    public function deleteMeasurement(string $id);
}
