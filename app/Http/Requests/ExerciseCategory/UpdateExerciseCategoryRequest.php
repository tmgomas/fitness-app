<?php

namespace App\Http\Requests\ExerciseCategory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'measurement_type' => ['sometimes', 'required', 'string', 'max:255'],
        ];
    }
}
