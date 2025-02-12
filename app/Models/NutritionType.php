<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NutritionType extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $primaryKey = 'nutrition_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'unit',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function foodNutrition(): HasMany
    {
        return $this->hasMany(FoodNutrition::class, 'nutrition_id', 'nutrition_id');
    }

    public function mealNutrition(): HasMany
    {
        return $this->hasMany(MealNutrition::class, 'nutrition_id', 'nutrition_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
