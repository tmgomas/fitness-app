<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoodItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'food_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'serving_size',
        'serving_unit',
        'weight_per_serving',  // g වලින් එක සේවින් එකක බර
        'image_url',
        'is_active'
    ];

    protected $casts = [
        'serving_size' => 'decimal:2',
        'weight_per_serving' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function foodNutrition(): HasMany
    {
        return $this->hasMany(FoodNutrition::class, 'food_id', 'food_id');
    }

    public function mealFoods(): HasMany
    {
        return $this->hasMany(MealFood::class, 'food_id', 'food_id');
    }
}
