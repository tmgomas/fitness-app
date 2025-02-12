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
                'required',
                'string',
                'max:255',
                Rule::unique('nutrition_types', 'name')->ignore($this->nutrition_type->nutrition_id, 'nutrition_id')
            ],
            'description' => ['required', 'string'],
            'unit' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean']
        ];
    }
}
