<?php

namespace App\Http\Requests\ExerciseIntensity;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseIntensityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'calorie_multiplier' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'string'],
        ];
    }
}
