<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAgreementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'version' => [
                'required',
                'string',
                'max:20',
                Rule::unique('agreements')->ignore($this->route('agreement'))
            ],
            'is_active' => 'boolean',
            'effective_date' => 'required|date',
            'summary' => 'nullable|string'
        ];
    }
}