<?php

// app/Http/Requests/UserPreference/StoreUserPreferenceRequest.php
namespace App\Http\Requests\UserPreference;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferenceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'allergies' => 'nullable|string',
            'dietary_restrictions' => 'nullable|string',
            'disliked_foods' => 'nullable|string',
            'fitness_goals' => 'required|string',
            'activity_level' => 'required|string'
        ];
    }
}
