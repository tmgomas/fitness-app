<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodNutrition extends Model
{
    use HasUuids;

    protected $primaryKey = 'food_nutrition_id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'food_id',
        'nutrition_id',
        'amount_per_100g',
        'measurement_unit'
    ];

    protected $casts = [
        'amount_per_100g' => 'decimal:2'
    ];

    public function food(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class, 'food_id', 'food_id');
    }

    public function nutritionType(): BelongsTo
    {
        return $this->belongsTo(NutritionType::class, 'nutrition_id', 'nutrition_id');
    }
}
