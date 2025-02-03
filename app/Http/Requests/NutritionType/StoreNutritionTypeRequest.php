<?php

namespace App\Http\Requests\NutritionType;

use Illuminate\Foundation\Http\FormRequest;

class StoreNutritionTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:nutrition_types'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ];
    }
}