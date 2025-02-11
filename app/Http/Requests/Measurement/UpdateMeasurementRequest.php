<?php

namespace App\Http\Requests\Measurement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\UserMeasurement;

class UpdateMeasurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $measurementId = $this->route('measurement');
        
        // Check if the measurement belongs to the authenticated user
        $measurement = UserMeasurement::where('measurement_id', $measurementId)
            ->where('user_id', Auth::id())
            ->exists();

        return $measurement;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'chest' => 'nullable|numeric|between:0,500',
            'waist' => 'nullable|numeric|between:0,500',
            'hips' => 'nullable|numeric|between:0,500',
            'arms' => 'nullable|numeric|between:0,500',
            'thighs' => 'nullable|numeric|between:0,500',
            'recorded_at' => 'required|date|before_or_equal:now'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'chest.numeric' => 'The chest measurement must be a number',
            'chest.between' => 'The chest measurement must be between 0 and 500',
            'waist.numeric' => 'The waist measurement must be a number',
            'waist.between' => 'The waist measurement must be between 0 and 500',
            'hips.numeric' => 'The hips measurement must be a number',
            'hips.between' => 'The hips measurement must be between 0 and 500',
            'arms.numeric' => 'The arms measurement must be a number',
            'arms.between' => 'The arms measurement must be between 0 and 500',
            'thighs.numeric' => 'The thighs measurement must be a number',
            'thighs.between' => 'The thighs measurement must be between 0 and 500',
            'recorded_at.required' => 'The recorded date is required',
            'recorded_at.date' => 'The recorded date must be a valid date',
            'recorded_at.before_or_equal' => 'The recorded date cannot be in the future'
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            if ($this->hasAnyMeasurement()) {
                return;
            }

            $validator->errors()->add('measurements', 'At least one measurement field must be provided');
        });
    }

    /**
     * Check if at least one measurement field is provided
     */
    private function hasAnyMeasurement(): bool
    {
        return $this->filled('chest') ||
            $this->filled('waist') ||
            $this->filled('hips') ||
            $this->filled('arms') ||
            $this->filled('thighs');
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Convert empty strings to null for measurement fields
        $this->merge(array_map(function ($value) {
            return $value === '' ? null : $value;
        }, $this->all()));
    }
}