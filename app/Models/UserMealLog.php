<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMealLog extends Model
{
    use HasFactory;

    protected $table = 'user_meal_logs';
    protected $primaryKey = 'meal_log_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'meal_log_id',
        'user_id',
        'meal_id',
        'date',
        'meal_type',
        'serving_size',
        'serving_unit',
    ];

    protected $casts = [
        'date' => 'datetime',
        'created_at' => 'datetime',
        'serving_size' => 'float',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id', 'meal_id');
    }
}
