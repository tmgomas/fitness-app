<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ExerciseIntensity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'calorie_multiplier',
        'description',
    ];

    protected $casts = [
        'calorie_multiplier' => 'float',
    ];
}