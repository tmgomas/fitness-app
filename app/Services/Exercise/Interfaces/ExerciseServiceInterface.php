<?php

namespace App\Services\Exercise\Interfaces;

use App\Models\Exercise;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExerciseServiceInterface
{
    public function getAllExercises(): LengthAwarePaginator;

    public function createExercise(array $data, ?UploadedFile $image = null): Exercise;

    public function updateExercise(string $id, array $data, ?UploadedFile $image = null): Exercise;

    public function deleteExercise(string $id): bool;

    public function toggleStatus(string $id): Exercise;

    public function searchExercises(string $query): LengthAwarePaginator;

    public function getByCategory(string $categoryId): LengthAwarePaginator;
}
