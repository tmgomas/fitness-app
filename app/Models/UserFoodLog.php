<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFoodLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'food_log_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'food_log_id',
        'user_id',
        'food_id',
        'date',
        'meal_type',
        'serving_size',
        'serving_unit'
    ];

    protected $casts = [
        'date' => 'datetime',
        'serving_size' => 'float'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function foodItem()
    {
        return $this->belongsTo(FoodItem::class, 'food_id', 'food_id');
    }
}
