<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class UserCustomExercise extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'difficulty_level',
        'image_url',
        'calories_per_minute',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'calories_per_minute' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exerciseLogs()
    {
        return $this->hasMany(UserExerciseLog::class, 'custom_exercise_id', 'id');
    }
}
