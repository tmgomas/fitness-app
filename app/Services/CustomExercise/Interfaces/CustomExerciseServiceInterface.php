<?php

namespace App\Services\CustomExercise\Interfaces;

interface CustomExerciseServiceInterface
{
    public function getAllCustomExercises();
    public function getCustomExercise(string $id);
    public function createCustomExercise(array $data);
    public function updateCustomExercise(string $id, array $data);
    public function deleteCustomExercise(string $id);
}
