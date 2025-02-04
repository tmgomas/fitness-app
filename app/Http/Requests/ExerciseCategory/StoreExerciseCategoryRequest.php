<?php

namespace App\Http\Requests\ExerciseCategory;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'measurement_type' => ['required', 'string', 'max:255'],
        ];
    }
}