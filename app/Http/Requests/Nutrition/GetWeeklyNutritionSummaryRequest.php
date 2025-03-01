<?php

namespace App\Http\Requests\Nutrition;

use Illuminate\Foundation\Http\FormRequest;

class GetWeeklyNutritionSummaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Assumes Sanctum middleware is already handling authentication
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'start_date' => 'nullable|date_format:Y-m-d',
            'end_date' => 'nullable|date_format:Y-m-d|after_or_equal:start_date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'start_date.date_format' => 'The start date must be in the format YYYY-MM-DD',
            'end_date.date_format' => 'The end date must be in the format YYYY-MM-DD',
            'end_date.after_or_equal' => 'The end date must be on or after the start date',
        ];
    }
}
