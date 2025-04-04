<?php

namespace App\Services\Exercise\Interfaces;

use App\Models\Exercise;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;

interface ExerciseServiceInterface
{
    /**
     * Get all exercises including user's custom exercises
     * 
     * @return LengthAwarePaginator Combined paginated results
     */
    public function getAllExercises(): LengthAwarePaginator;

    public function createExercise(array $data, ?UploadedFile $image = null): Exercise;

    public function updateExercise(string $id, array $data, ?UploadedFile $image = null): Exercise;

    public function deleteExercise(string $id): bool;

    public function toggleStatus(string $id): Exercise;

    /**
     * Search exercises including user's custom exercises
     * 
     * @param string $query The search term
     * @return LengthAwarePaginator Combined paginated results
     */
    public function searchExercises(string $query): LengthAwarePaginator;

    public function getByCategory(string $categoryId): LengthAwarePaginator;
}