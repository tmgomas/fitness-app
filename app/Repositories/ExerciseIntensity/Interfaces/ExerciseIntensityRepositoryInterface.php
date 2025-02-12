<?php

// app/Repositories/ExerciseIntensity/Interfaces/ExerciseIntensityRepositoryInterface.php

namespace App\Repositories\ExerciseIntensity\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface ExerciseIntensityRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get intensity by name
     *
     * @param string $name
     * @return mixed
     */
    public function findByName(string $name);

    /**
     * Get intensities by calorie multiplier range
     *
     * @param float $min
     * @param float $max
     * @return mixed
     */
    public function findByCalorieMultiplierRange(float $min, float $max);

    /**
     * Search intensities by name or description
     *
     * @param string $searchTerm
     * @return mixed
     */
    public function search(string $searchTerm);

    /**
     * Get active intensities sorted by calorie multiplier
     *
     * @param string $direction
     * @return mixed
     */
    public function getAllSortedByCalorieMultiplier(string $direction = 'asc');

    /**
     * Get intensities with calorie multiplier greater than value
     *
     * @param float $value
     * @return mixed
     */
    public function getIntensitiesAboveMultiplier(float $value);
}
