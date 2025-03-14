<?php

namespace App\Http\Requests\CustomExercise;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomExerciseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'nullable|string|in:beginner,intermediate,advanced',
            'calories_per_minute' => 'sometimes|required|numeric|min:0|max:50',
            'is_active' => 'boolean'
        ];
    }
}
