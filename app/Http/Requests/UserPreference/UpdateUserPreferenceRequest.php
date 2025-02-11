<?php

// app/Http/Requests/UserPreference/UpdateUserPreferenceRequest.php

namespace App\Http\Requests\UserPreference;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPreferenceRequest extends FormRequest
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
            'fitness_goals' => 'nullable|string',
            'activity_level' => 'nullable|string'
        ];
    }
}
