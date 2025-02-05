<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class UserHealthData extends Model
{
    use HasUuids, HasApiTokens;

    protected $primaryKey = 'health_id';
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'height',
        'weight',
        'bmi',
        'blood_type',
        'medical_conditions',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'height' => 'float',
        'weight' => 'float',
        'bmi' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
