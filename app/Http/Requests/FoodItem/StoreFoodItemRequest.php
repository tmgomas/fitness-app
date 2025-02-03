<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;

class StoreFoodItemRequest extends FormRequest
{
    public function authorize(): bool
    {
       
        return true;
    }

    public function rules(): array
    {
        return [
            // Food Item Validation
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'serving_size' => ['required', 'numeric', 'min:0'],
            'serving_unit' => ['required', 'string', 'max:50'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],

            // Nutrition Validation
            'nutrition' => ['array'],
            'nutrition.*.nutrition_id' => ['required', 'uuid', 'exists:nutrition_types,nutrition_id'],
            'nutrition.*.amount_per_100g' => ['nullable', 'numeric', 'min:0'],
            'nutrition.*.measurement_unit' => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nutrition.*.nutrition_id.required' => 'The nutrition type is required.',
            'nutrition.*.nutrition_id.exists' => 'The selected nutrition type is invalid.',
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