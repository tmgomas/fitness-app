<?php
// app/Repositories/NutritionType/Interfaces/NutritionTypeRepositoryInterface.php
namespace App\Repositories\NutritionType\Interfaces;

interface NutritionTypeRepositoryInterface
{
    public function getAllActive($perPage = 10);
    public function getById($nutritionId);
    public function create(array $data);
    public function update($nutritionId, array $data);
    public function delete($nutritionId);
    public function findByName(string $name);
}
