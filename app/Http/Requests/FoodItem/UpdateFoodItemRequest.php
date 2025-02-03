<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFoodItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Food Item Validation
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'serving_size' => ['sometimes', 'required', 'numeric', 'min:0'],
            'serving_unit' => ['sometimes', 'required', 'string', 'max:50'],
            'image_url' => ['sometimes', 'nullable', 'url', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
    
            // Nutrition Validation
            'nutrition' => ['array'],
            'nutrition.*.nutrition_id' => ['required', 'uuid', 'exists:nutrition_types,nutrition_id'],  // Changed from 'id' to 'nutrition_id'
            'nutrition.*.food_nutrition_id' => ['nullable', 'uuid', 'exists:food_nutrition,food_nutrition_id'],
            'nutrition.*.amount_per_100g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.*.measurement_unit' => ['required', 'string', 'max:50'],
        ];
    }
    public function messages(): array
    {
        return [
            'nutrition.*.nutrition_id.required' => 'The nutrition type is required.',
            'nutrition.*.nutrition_id.exists' => 'The selected nutrition type is invalid.',
            'nutrition.*.food_nutrition_id.exists' => 'The nutrition record does not exist.',
            'nutrition.*.amount_per_100g.numeric' => 'The amount must be a number.',
            'nutrition.*.amount_per_100g.min' => 'The amount cannot be negative.',
            'nutrition.*.measurement_unit.required' => 'The measurement unit is required.',
            'nutrition.*.measurement_unit.max' => 'The measurement unit cannot exceed 50 characters.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => true
            ]);
        } else {
            $this->merge([
                'is_active' => false
            ]);
        }
    }
}