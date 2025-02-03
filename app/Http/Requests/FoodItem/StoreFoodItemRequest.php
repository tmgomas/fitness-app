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
            'name' => ['required', 'string', 'max:255', 'unique:food_items'],
            'description' => ['nullable', 'string'],
            'serving_size' => ['required', 'numeric', 'min:0'],
            'serving_unit' => ['required', 'string', 'max:50'],
            'image_url' => ['nullable', 'url', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }
}