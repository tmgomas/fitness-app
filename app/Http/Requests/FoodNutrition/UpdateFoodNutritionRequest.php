<?php

namespace App\Http\Requests\FoodNutrition;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodNutritionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'food_id' => ['sometimes', 'required', 'uuid', 'exists:food_items,id'],
            'nutrition_id' => ['sometimes', 'required', 'uuid', 'exists:nutrition_types,id'],
            'amount_per_100g' => ['sometimes', 'required', 'numeric', 'min:0'],
            'measurement_unit' => ['sometimes', 'required', 'string', 'max:50'],
        ];
    }
}