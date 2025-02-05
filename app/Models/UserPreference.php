<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Laravel\Sanctum\HasApiTokens;

class UserPreference extends Model
{
    use HasUuids, HasApiTokens;

    protected $primaryKey = 'pref_id';
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'allergies',
        'dietary_restrictions',
        'disliked_foods',
        'fitness_goals',
        'updated_at'
    ];

    protected $casts = [
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
