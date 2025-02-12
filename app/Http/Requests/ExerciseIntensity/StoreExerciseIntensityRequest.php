<?php

// app/Http/Requests/ExerciseIntensity/StoreExerciseIntensityRequest.php

namespace App\Http\Requests\ExerciseIntensity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExerciseIntensityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Modify based on your authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('exercise_intensities', 'name'),
            ],
            'calorie_multiplier' => [
                'required',
                'numeric',
                'min:0.1',
                'max:10.0'
            ],
            'description' => [
                'required',
                'string',
                'max:1000'
            ],
            'is_active' => [
                'boolean',
                'nullable'
            ],
            'sort_order' => [
                'integer',
                'min:0',
                'nullable'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The intensity name is required.',
            'name.unique' => 'This intensity name already exists.',
            'calorie_multiplier.required' => 'The calorie multiplier is required.',
            'calorie_multiplier.min' => 'The calorie multiplier must be at least 0.1.',
            'calorie_multiplier.max' => 'The calorie multiplier cannot exceed 10.0.',
            'description.required' => 'A description of the intensity is required.',
            'sort_order.integer' => 'The sort order must be a whole number.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->is_active ?? true,
            'sort_order' => $this->sort_order ?? 0,
        ]);
    }
}
