<?php

namespace App\Http\Requests\UserProfile;

use Illuminate\Foundation\Http\FormRequest;

class UploadProfilePictureRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048']
        ];
    }
}
