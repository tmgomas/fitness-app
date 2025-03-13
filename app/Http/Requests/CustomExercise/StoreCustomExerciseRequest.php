<?php

namespace App\Http\Requests\CustomExercise;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomExerciseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty_level' => 'nullable|string|in:beginner,intermediate,advanced,expert',
            'calories_per_minute' => 'required|numeric|min:0|max:50',
            'is_active' => 'boolean'
        ];
    }
}
