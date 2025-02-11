<?php

// app/Http/Requests/HealthData/StoreHealthDataRequest.php
namespace App\Http\Requests\HealthData;

use Illuminate\Foundation\Http\FormRequest;

class StoreHealthDataRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'height' => 'required|numeric',
            'weight' => 'required|numeric',
            'bmi' => 'required|numeric',
            'blood_type' => 'required|string',
            'medical_conditions' => 'nullable|string',
            'recorded_at' => 'required|date'
        ];
    }
}
