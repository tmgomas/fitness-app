<?php

// app/Services/HealthData/Interfaces/HealthDataServiceInterface.php
namespace App\Services\HealthData\Interfaces;

interface HealthDataServiceInterface
{
    public function getAllHealthData();
    public function storeHealthData(array $data);
    public function getHealthData($healthId);
    public function updateHealthData($healthId, array $data);
    public function deleteHealthData($healthId);
}
