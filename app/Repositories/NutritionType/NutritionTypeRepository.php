<?php
// app/Repositories/NutritionType/NutritionTypeRepository.php
namespace App\Repositories\NutritionType;

use App\Models\NutritionType;
use App\Repositories\Base\BaseRepository;
use App\Repositories\NutritionType\Interfaces\NutritionTypeRepositoryInterface;

class NutritionTypeRepository extends BaseRepository implements NutritionTypeRepositoryInterface
{
    public function __construct(NutritionType $model)
    {
        parent::__construct($model);
    }

    public function getAllActive($perPage = 10)
    {
        return $this->model->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    public function getById($nutritionId)
    {
        return $this->model->where('nutrition_id', $nutritionId)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($nutritionId, array $data)
    {
        $nutritionType = $this->getById($nutritionId);
        $nutritionType->update($data);
        return $nutritionType;
    }

    public function delete($nutritionId)
    {
        $nutritionType = $this->getById($nutritionId);
        return $nutritionType->delete();
    }

    public function findByName(string $name)
    {
        return $this->model->where('name', $name)->first();
    }
}
