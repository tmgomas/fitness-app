<?php

namespace App\Http\Requests\ExerciseIntensity;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseIntensityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'calorie_multiplier' => ['sometimes', 'required', 'numeric', 'min:0'],
            'description' => ['sometimes', 'required', 'string'],
        ];
    }
}