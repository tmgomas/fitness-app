<?php

namespace App\Http\Requests\UserFoodLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserFoodLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'food_id' => 'required|string|exists:food_items,food_id',
            'date' => 'required|date',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'required|numeric|min:0',
            'serving_unit' => 'required|string'
        ];
    }

    public function messages()
    {
        return [
            'food_id.required' => 'A food item must be selected',
            'food_id.exists' => 'The selected food item is invalid',
            'date.required' => 'The date is required',
            'date.date' => 'Please provide a valid date',
            'meal_type.required' => 'The meal type is required',
            'meal_type.in' => 'The meal type must be breakfast, lunch, dinner, or snack',
            'serving_size.required' => 'The serving size is required',
            'serving_size.numeric' => 'The serving size must be a number',
            'serving_size.min' => 'The serving size must be greater than 0',
            'serving_unit.required' => 'The serving unit is required'
        ];
    }
}
