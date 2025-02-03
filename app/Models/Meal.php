<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Meal extends Model
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'meal_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'image_url',
        'default_serving_size',
        'serving_unit',
        'is_active'
    ];

    protected $casts = [
        'default_serving_size' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function nutritionFacts(): HasMany
    {
        return $this->hasMany(MealNutrition::class, 'meal_id', 'meal_id');
    }

    public function foods(): HasMany
    {
        return $this->hasMany(MealFood::class, 'meal_id', 'meal_id');
    }
}
