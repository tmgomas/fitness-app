<?php
// app/Models/Exercise.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exercise extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'difficulty_level',
        'image_url',
        'calories_per_minute',
        'calories_per_km',
        'requires_distance',
        'requires_heartrate',
        'recommended_intensity',
        'is_active',
    ];



    protected $casts = [
        'requires_distance' => 'boolean',
        'requires_heartrate' => 'boolean',
        'is_active' => 'boolean',
        'calories_per_minute' => 'float',
        'calories_per_km' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ExerciseCategory::class, 'category_id', 'id');
    }
}
