<?php

namespace App\Http\Requests\UserExerciseLog;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserExerciseLogRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'exercise_id' => 'nullable|string|exists:exercises,id',
            'custom_exercise_id' => 'nullable|string|exists:user_custom_exercises,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'distance' => 'nullable|numeric|min:0',
            'distance_unit' => 'required_with:distance|in:km,mi',
            'avg_heart_rate' => 'nullable|numeric|min:0|max:250',
            'intensity_level' => 'required|in:low,medium,high,moderate',
            'notes' => 'nullable|string|max:500'
        ];
    }

    public function messages()
    {
        return [
            'exercise_id.required' => 'An exercise must be selected',
            'exercise_id.exists' => 'The selected exercise is invalid',
            'custom_exercise_id.exists' => 'The selected custom exercise is invalid',
            'start_time.required' => 'Start time is required',
            'end_time.required' => 'End time is required',
            'end_time.after' => 'End time must be after start time',
            'distance.numeric' => 'Distance must be a number',
            'distance.min' => 'Distance cannot be negative',
            'distance_unit.required_with' => 'Distance unit is required when distance is provided',
            'distance_unit.in' => 'Distance unit must be either km or mi',
            'avg_heart_rate.numeric' => 'Heart rate must be a number',
            'avg_heart_rate.min' => 'Heart rate cannot be negative',
            'avg_heart_rate.max' => 'Heart rate cannot exceed 250',
            'intensity_level.required' => 'Intensity level is required',
            'intensity_level.in' => 'Intensity level must be low, medium, or high',
            'notes.max' => 'Notes cannot exceed 500 characters'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (empty($this->exercise_id) && empty($this->custom_exercise_id)) {
                $validator->errors()->add('exercise', 'Either a standard exercise or custom exercise must be selected');
            }
        });
    }
}
