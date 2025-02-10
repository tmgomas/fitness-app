<?php

namespace App\Http\Requests\Meal;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreMealRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'default_serving_size' => 'required|numeric|min:0.01',
            'serving_unit' => 'required|string|max:50',
            'is_active' => 'boolean',

            // Nutrition Facts Validation
            'nutrition_facts' => 'required|array',
            'nutrition_facts.*.nutrition_id' => 'required|exists:nutrition_types,nutrition_id',
            'nutrition_facts.*.amount_per_100g' => 'nullable|numeric|min:0',
            'nutrition_facts.*.measurement_unit' => 'required|string|max:20',

            // Food Items Validation
            'foods' => 'nullable|array',
            'foods.*.food_id' => 'exists:food_items,food_id',
            'foods.*.quantity' => 'numeric|min:0.01',
            'foods.*.unit' => 'string|max:20'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            // Basic Information Messages
            'name.required' => 'Meal name is required',
            'name.max' => 'Meal name cannot exceed 255 characters',
            'image.image' => 'File must be an image',
            'image.mimes' => 'Image must be a type of: jpeg, png, jpg, gif',
            'image.max' => 'Image must not exceed 2MB',
            'default_serving_size.required' => 'Default serving size is required',
            'default_serving_size.numeric' => 'Default serving size must be a number',
            'default_serving_size.min' => 'Default serving size must be at least 0.01',
            'serving_unit.required' => 'Serving unit is required',
            'serving_unit.max' => 'Serving unit cannot exceed 50 characters',

            // Nutrition Facts Messages
            'nutrition_facts.required' => 'Nutrition facts are required',
            'nutrition_facts.array' => 'Nutrition facts must be an array',
            'nutrition_facts.*.nutrition_id.required' => 'Nutrition type ID is required',
            'nutrition_facts.*.nutrition_id.exists' => 'Invalid nutrition type selected',
            'nutrition_facts.*.amount_per_100g.numeric' => 'Amount must be a number',
            'nutrition_facts.*.amount_per_100g.min' => 'Amount cannot be negative',
            'nutrition_facts.*.measurement_unit.required' => 'Measurement unit is required',
            'nutrition_facts.*.measurement_unit.max' => 'Measurement unit cannot exceed 20 characters',

            // Food Items Messages
            'foods.array' => 'Food items must be an array',
            'foods.*.food_id.exists' => 'Invalid food item selected',
            'foods.*.quantity.numeric' => 'Quantity must be a number',
            'foods.*.quantity.min' => 'Quantity must be at least 0.01',
            'foods.*.unit.max' => 'Unit cannot exceed 20 characters'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        Log::info('PrepareForValidation - Before:', [
            'request_data' => $this->all()
        ]);

        // Handle is_active checkbox
        $this->merge([
            'is_active' => $this->boolean('is_active')
        ]);

        // Clean up empty food items if any
        if ($this->has('foods')) {
            $foods = array_values(array_filter($this->foods, function ($food) {
                return !empty($food['food_id']);
            }));
            $this->merge(['foods' => $foods]);
        }

        // Convert empty nutrition values to null
        if ($this->has('nutrition_facts')) {
            $nutrition_facts = array_map(function ($nutrition) {
                if (empty($nutrition['amount_per_100g'])) {
                    $nutrition['amount_per_100g'] = null;
                }
                return $nutrition;
            }, $this->nutrition_facts);
            $this->merge(['nutrition_facts' => $nutrition_facts]);
        }

        Log::info('PrepareForValidation - After:', [
            'request_data' => $this->all()
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation(): void
    {
        Log::info('Validation passed. Final data:', [
            'validated_data' => $this->validated(),
            'all_data' => $this->all()
        ]);
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        Log::error('Validation failed:', [
            'errors' => $validator->errors()->toArray(),
            'data' => $this->all()
        ]);

        parent::failedValidation($validator);
    }

    /**
     * Get data to be validated from the request.
     */
    public function validationData()
    {
        return array_merge($this->all(), [
            'nutrition_facts' => array_values($this->nutrition_facts ?? []),
            'foods' => array_values($this->foods ?? [])
        ]);
    }
}
