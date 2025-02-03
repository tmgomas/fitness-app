<?php

namespace App\Http\Requests\FoodNutrition;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodNutritionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // dd('here');
        return true;
    }

    public function rules(): array
    {
        return [
            'food_id' => ['required', 'uuid', 'exists:food_items,food_id'],
            'nutrition_id' => ['required', 'uuid', 'exists:nutrition_types,nutrition_id'],
            'amount_per_100g' => ['required', 'numeric', 'min:0'],
            'measurement_unit' => ['required', 'string', 'max:50'],
        ];
    }

    protected function prepareForValidation(): void
{
    $this->merge([
        'is_active' => $this->has('is_active'),
    ]);
}

}
