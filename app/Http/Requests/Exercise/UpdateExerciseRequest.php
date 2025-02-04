<?php

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'required', 'uuid', 'exists:exercise_categories,id'],
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'difficulty_level' => ['sometimes', 'required', 'string', 'max:255'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'calories_per_minute' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'calories_per_km' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'requires_distance' => ['sometimes', 'required', 'boolean'],
            'requires_heartrate' => ['sometimes', 'required', 'boolean'],
            'recommended_intensity' => ['sometimes', 'required', 'string', 'max:255'],
            'is_active' => ['sometimes', 'required', 'boolean'],
        ];
    }
}
