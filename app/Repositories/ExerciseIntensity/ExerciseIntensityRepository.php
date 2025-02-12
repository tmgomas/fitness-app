<?php

// app/Repositories/ExerciseIntensity/ExerciseIntensityRepository.php

namespace App\Repositories\ExerciseIntensity;

use App\Models\ExerciseIntensity;
use App\Repositories\Base\BaseRepository;
use App\Repositories\ExerciseIntensity\Interfaces\ExerciseIntensityRepositoryInterface;

class ExerciseIntensityRepository extends BaseRepository implements ExerciseIntensityRepositoryInterface
{
    public function __construct(ExerciseIntensity $model)
    {
        parent::__construct($model);
    }

    public function findByName(string $name)
    {
        return $this->model
            ->where('name', $name)
            ->first();
    }

    public function findByCalorieMultiplierRange(float $min, float $max)
    {
        return $this->model
            ->whereBetween('calorie_multiplier', [$min, $max])
            ->get();
    }

    public function search(string $searchTerm)
    {
        return $this->model
            ->where('name', 'LIKE', "%{$searchTerm}%")
            ->orWhere('description', 'LIKE', "%{$searchTerm}%")
            ->get();
    }

    public function getAllSortedByCalorieMultiplier(string $direction = 'asc')
    {
        return $this->model
            ->orderBy('calorie_multiplier', $direction)
            ->get();
    }

    public function getIntensitiesAboveMultiplier(float $value)
    {
        return $this->model
            ->where('calorie_multiplier', '>', $value)
            ->get();
    }
}
