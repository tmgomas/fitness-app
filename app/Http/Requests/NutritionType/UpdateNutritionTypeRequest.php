<?php

namespace App\Http\Requests\NutritionType;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateNutritionTypeRequest extends FormRequest
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
                Rule::unique('nutrition_types')->ignore($this->nutritionType)
            ],
            'description' => ['string'],
            'unit' => ['string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}