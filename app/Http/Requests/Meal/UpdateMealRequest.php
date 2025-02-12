<?php

namespace App\Http\Requests\Meal;

use Illuminate\Support\Facades\Log;

class UpdateMealRequest extends StoreMealRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = parent::rules();

        // Make image validation optional for updates
        $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048';

        // Add meal_id validation - using route parameter
        $rules['meal_id'] = 'required|exists:meals,meal_id';

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'meal_id.required' => 'Meal ID is required',
            'meal_id.exists' => 'Invalid meal selected',
        ]);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        Log::info('UpdateMealRequest - Before preparation:', [
            'request_data' => $this->all()
        ]);

        // Get meal_id from route parameter
        $mealId = $this->route('meal');

        // Add meal_id to request data if it's a valid string
        if (is_string($mealId)) {
            $this->merge(['meal_id' => $mealId]);
        }

        // Handle is_active checkbox
        $this->merge([
            'is_active' => $this->boolean('is_active')
        ]);

        // Clean up empty food items
        if ($this->has('foods')) {
            $foods = array_values(array_filter($this->foods, function ($food) {
                return !empty($food['food_id']);
            }));
            $this->merge(['foods' => $foods]);
        }

        // Handle nutrition facts
        if ($this->has('nutrition_facts')) {
            $nutrition_facts = array_map(function ($nutrition) {
                if (empty($nutrition['amount_per_100g'])) {
                    $nutrition['amount_per_100g'] = null;
                }
                return $nutrition;
            }, $this->nutrition_facts);

            $nutrition_facts = array_filter($nutrition_facts, function ($nutrition) {
                return !is_null($nutrition['amount_per_100g']) || !isset($nutrition['exists']);
            });

            $this->merge(['nutrition_facts' => array_values($nutrition_facts)]);
        }

        Log::info('UpdateMealRequest - After preparation:', [
            'request_data' => $this->all()
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->foods) && empty($this->nutrition_facts)) {
                $validator->errors()->add('general', 'At least one food item or nutrition fact must be specified');
            }
        });
    }

    /**
     * Get data to be validated from the request.
     */
    public function validationData()
    {
        $data = parent::validationData();

        // Get meal_id from route parameter
        $mealId = $this->route('meal');

        // Only add meal_id if it's a valid string
        if (is_string($mealId)) {
            $data['meal_id'] = $mealId;
        }

        return $data;
    }
}
