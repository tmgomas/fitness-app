<?php

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'uuid', 'exists:exercise_categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'difficulty_level' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'calories_per_minute' => ['nullable', 'numeric', 'min:0'],
            'calories_per_km' => ['nullable', 'numeric', 'min:0'],
            'requires_distance' => ['required', 'boolean'],
            'requires_heartrate' => ['required', 'boolean'],
            'recommended_intensity' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
