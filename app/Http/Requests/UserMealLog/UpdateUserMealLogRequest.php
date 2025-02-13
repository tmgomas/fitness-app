<?php

namespace App\Http\Requests\UserMealLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserMealLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'meal_id' => 'sometimes|string|exists:meals,meal_id',
            'date' => 'sometimes|date',
            'meal_type' => 'sometimes|string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'sometimes|numeric|min:0',
            'serving_unit' => 'sometimes|string',
        ];
    }

    public function messages()
    {
        return [
            'meal_id.exists' => 'The selected meal is invalid',
            'date.date' => 'Please provide a valid date',
            'meal_type.in' => 'The meal type must be breakfast, lunch, dinner, or snack',
            'serving_size.numeric' => 'The serving size must be a number',
            'serving_size.min' => 'The serving size must be greater than 0',
        ];
    }
}
