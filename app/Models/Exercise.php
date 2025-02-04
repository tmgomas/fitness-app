<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exercise extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'difficulty_level',
        'image_url',
        'calories_per_minute',
        'calories_per_km',
        'requires_distance',
        'requires_heartrate',
        'recommended_intensity',
        'is_active',
    ];

    protected $casts = [
        'requires_distance' => 'boolean',
        'requires_heartrate' => 'boolean',
        'is_active' => 'boolean',
        'calories_per_minute' => 'float',
        'calories_per_km' => 'float',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class, 'category_id');
    }
}