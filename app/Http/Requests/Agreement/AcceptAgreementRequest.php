<?php

namespace App\Http\Requests\Agreement;

use Illuminate\Foundation\Http\FormRequest;

class AcceptAgreementRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'agreement_id' => 'required|exists:agreements,id'
        ];
    }
}