<?php

namespace App\Http\Requests\Exercise;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'uuid',
                'exists:exercise_categories,id'
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exercises', 'name')->ignore($this->exercise)
            ],
            'description' => [
                'required',
                'string',
                'min:10'
            ],
            'difficulty_level' => [
                'required',
                'string',
                'max:255',
                Rule::in(['beginner', 'intermediate', 'advanced', 'expert'])
            ],
            'image' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'calories_per_minute' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000'
            ],
            'calories_per_km' => [
                'nullable',
                'numeric',
                'min:0',
                'max:1000'
            ],
            'requires_distance' => [
                'required',
                'boolean'
            ],
            'requires_heartrate' => [
                'required',
                'boolean'
            ],
            'recommended_intensity' => [
                'required',
                'string',
                'max:255',
                Rule::in(['low', 'moderate', 'high', 'very_high'])
            ],
            'is_active' => [
                'required',
                'boolean'
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Handle checkbox values
        $this->merge([
            'requires_distance' => $this->boolean('requires_distance'),
            'requires_heartrate' => $this->boolean('requires_heartrate'),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'The exercise category is required.',
            'category_id.exists' => 'The selected exercise category is invalid.',
            'name.required' => 'The exercise name is required.',
            'name.unique' => 'This exercise name already exists.',
            'description.required' => 'The exercise description is required.',
            'description.min' => 'The description must be at least 10 characters.',
            'difficulty_level.required' => 'The difficulty level is required.',
            'difficulty_level.in' => 'The selected difficulty level is invalid.',
            'image.image' => 'The file must be an image.',
            'image.mimes' => 'The image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The image must not be greater than 2MB.',
            'calories_per_minute.numeric' => 'Calories per minute must be a number.',
            'calories_per_minute.min' => 'Calories per minute must be at least 0.',
            'calories_per_minute.max' => 'Calories per minute must not exceed 1000.',
            'calories_per_km.numeric' => 'Calories per kilometer must be a number.',
            'calories_per_km.min' => 'Calories per kilometer must be at least 0.',
            'calories_per_km.max' => 'Calories per kilometer must not exceed 1000.',
            'recommended_intensity.required' => 'The recommended intensity is required.',
            'recommended_intensity.in' => 'The selected recommended intensity is invalid.',
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' => 'exercise category',
            'requires_distance' => 'distance requirement',
            'requires_heartrate' => 'heart rate requirement',
            'is_active' => 'active status',
        ];
    }
}
