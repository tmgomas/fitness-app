<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class MealFood extends Model
{
    use HasUuids, SoftDeletes;

    protected $primaryKey = 'meal_foods_id';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'meal_foods';


    protected $fillable = [
        'meal_id',
        'food_id',
        'quantity',
        'unit'
    ];

    protected $casts = [
        'quantity' => 'decimal:2'
    ];

    public function meal(): BelongsTo
    {
        return $this->belongsTo(Meal::class, 'meal_id', 'meal_id');
    }

    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class, 'food_id', 'food_id');
    }
}