<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgreementRequest extends FormRequest
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
            'version' => 'required|string|max:20|unique:agreements,version',
            'is_active' => 'boolean',
            'effective_date' => 'required|date',
            'summary' => 'nullable|string'
        ];
    }
}