<?php

namespace App\Repositories\CustomExercise\Interfaces;

use App\Repositories\Interfaces\BaseRepositoryInterface;

interface CustomExerciseRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllByUserId(int $userId);
    public function findByIdAndUser(string $id, int $userId);
}
