<?php

// app/Models/UserExerciseLog.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserExerciseLog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'exercise_id',
        'custom_exercise_id', // Added this new field
        'start_time',
        'end_time',
        'duration_minutes',
        'distance',
        'distance_unit',
        'calories_burned',
        'avg_heart_rate',
        'intensity_level',
        'notes'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_minutes' => 'float',
        'distance' => 'float',
        'calories_burned' => 'float',
        'avg_heart_rate' => 'float',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    // Add this new relation
    public function customExercise()
    {
        return $this->belongsTo(UserCustomExercise::class, 'custom_exercise_id', 'id');
    }

    // Add this helper method
    public function getExerciseDetails()
    {
        if ($this->exercise_id && $this->exercise) {
            return $this->exercise;
        } elseif ($this->custom_exercise_id && $this->customExercise) {
            return $this->customExercise;
        }
        return null;
    }
}
