<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExerciseCategory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'measurement_type',
    ];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'category_id');
    }
}
