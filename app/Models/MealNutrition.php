<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealNutrition extends Model
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'meal_nutrition_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $table = 'meal_nutrition';

    protected $fillable = [
        'meal_id',
        'nutrition_id',
        'amount_per_100g',
        'measurement_unit'
    ];

    protected $casts = [
        'amount_per_100g' => 'decimal:2'
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'meal_id', 'meal_id');
    }

    public function nutritionType(): BelongsTo
    {
        return $this->belongsTo(NutritionType::class, 'nutrition_id', 'nutrition_id');
    }
}