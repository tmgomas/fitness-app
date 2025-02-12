<?php
// app/Repositories/NutritionType/Interfaces/NutritionTypeRepositoryInterface.php
namespace App\Repositories\NutritionType\Interfaces;

interface NutritionTypeRepositoryInterface
{
    public function getAllActive();
    public function getById($nutritionId);
    public function create(array $data);
    public function update($nutritionId, array $data);
    public function delete($nutritionId);
    public function findByName(string $name);
}