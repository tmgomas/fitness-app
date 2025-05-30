<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class UserMeasurement extends Model
{
    use HasUuids, HasApiTokens;

    protected $primaryKey = 'measurement_id';
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'chest',
        'waist',
        'hips',
        'arms',
        'thighs',
        'neck',   // Added new column
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'chest' => 'float',
        'waist' => 'float',
        'hips' => 'float',
        'arms' => 'float',
        'neck' => 'float',  // Added cast for new column
        'thighs' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
