<?php

namespace App\Http\Requests\FoodItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFoodItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'string', 
                'max:255',
                Rule::unique('food_items')->ignore($this->foodItem)
            ],
            'description' => ['nullable', 'string'],
            'serving_size' => ['numeric', 'min:0'],
            'serving_unit' => ['string', 'max:50'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}