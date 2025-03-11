<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore(Auth::id())
            ],
            'gender' => ['sometimes', 'nullable', 'string', 'in:male,female,other'],
            'birthday' => ['sometimes', 'nullable', 'date', 'before:today'],
        ];
    }
}
