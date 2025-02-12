<?php
// app/Repositories/Meal/Interfaces/MealRepositoryInterface.php
namespace App\Repositories\Meal\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface MealRepositoryInterface extends BaseRepositoryInterface
{
    public function getWithRelations($id);
    public function searchMeals(string $query, bool $isActive = true);
    public function createWithRelations(array $data);
    public function updateWithRelations($id, array $data);
}
