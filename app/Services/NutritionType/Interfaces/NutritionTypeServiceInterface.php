<?php
// app/Services/NutritionType/Interfaces/NutritionTypeServiceInterface.php
namespace App\Services\NutritionType\Interfaces;

interface NutritionTypeServiceInterface
{
    public function getAllNutritionTypes();
    public function getNutritionType($nutritionId);
    public function createNutritionType(array $data);
    public function updateNutritionType($nutritionId, array $data);
    public function deleteNutritionType($nutritionId);
}
