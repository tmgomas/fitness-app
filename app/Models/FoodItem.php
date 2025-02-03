<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FoodItem extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'serving_size',
        'serving_unit',
        'image_url',
        'is_active'
    ];

    protected $casts = [
        'serving_size' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}