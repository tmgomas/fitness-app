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
            'name' => ['required', 'string', 'max:255', 'unique:nutrition_types,name'],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The nutrition type name is required',
            'name.unique' => 'This nutrition type name already exists',
            'unit.required' => 'The unit of measurement is required'
        ];
    }
}
