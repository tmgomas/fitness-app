<?php

namespace App\Http\Requests\UserFoodLog;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserFoodLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'food_id' => 'sometimes|string|exists:food_items,food_id',
            'date' => 'sometimes|date',
            'meal_type' => 'sometimes|string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'sometimes|numeric|min:0',
            'serving_unit' => 'sometimes|string'
        ];
    }

    public function messages()
    {
        return [
            'food_id.exists' => 'The selected food item is invalid',
            'date.date' => 'Please provide a valid date',
            'meal_type.in' => 'The meal type must be breakfast, lunch, dinner, or snack',
            'serving_size.numeric' => 'The serving size must be a number',
            'serving_size.min' => 'The serving size must be greater than 0'
        ];
    }
}
