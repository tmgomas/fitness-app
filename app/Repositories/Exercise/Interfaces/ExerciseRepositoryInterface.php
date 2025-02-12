<?php

namespace App\Repositories\Exercise\Interfaces;

use App\Models\Exercise;
use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExerciseRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllWithPagination(int $perPage = 10): LengthAwarePaginator;
    public function search(string $query): LengthAwarePaginator;
    public function getByCategory(string $categoryId): LengthAwarePaginator;
}
