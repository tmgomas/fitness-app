<?php

namespace App\Http\Requests\UserMealLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserMealLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'meal_id' => 'required|string|exists:meals,meal_id',
            'date' => 'required|date',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'serving_size' => 'required|numeric|min:0',
            'serving_unit' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'meal_id.required' => 'A meal must be selected',
            'meal_id.exists' => 'The selected meal is invalid',
            'date.required' => 'The date is required',
            'date.date' => 'Please provide a valid date',
            'meal_type.required' => 'The meal type is required',
            'meal_type.in' => 'The meal type must be breakfast, lunch, dinner, or snack',
            'serving_size.required' => 'The serving size is required',
            'serving_size.numeric' => 'The serving size must be a number',
            'serving_size.min' => 'The serving size must be greater than 0',
            'serving_unit.required' => 'The serving unit is required',
        ];
    }
}
