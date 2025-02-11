<?php
// app/Http/Requests/HealthData/.php
namespace App\Http\Requests\HealthData;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHealthDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'height' => 'nullable|numeric',
            'weight' => 'nullable|numeric',
            'bmi' => 'nullable|numeric',
            'blood_type' => 'nullable|string',
            'medical_conditions' => 'nullable|string',
            'recorded_at' => 'required|date'
        ];
    }
}
