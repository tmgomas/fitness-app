<?php

// app/Models/ExerciseIntensity.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ExerciseIntensity extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'exercise_intensities';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'calorie_multiplier',
        'description',

    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'calorie_multiplier' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */


    /**
     * Get the exercise logs associated with this intensity.
     */
    public function exerciseLogs(): HasMany
    {
        return $this->hasMany(UserExerciseLog::class, 'intensity_level', 'id');
    }

    /**
     * Scope a query to only include active intensities.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include intensities with multiplier above a certain value.
     */
    public function scopeAboveMultiplier(Builder $query, float $value): Builder
    {
        return $query->where('calorie_multiplier', '>', $value);
    }

    /**
     * Scope a query to only include intensities with multiplier below a certain value.
     */
    public function scopeBelowMultiplier(Builder $query, float $value): Builder
    {
        return $query->where('calorie_multiplier', '<', $value);
    }

    /**
     * Scope a query to only include intensities within a multiplier range.
     */
    public function scopeMultiplierRange(Builder $query, float $min, float $max): Builder
    {
        return $query->whereBetween('calorie_multiplier', [$min, $max]);
    }

    /**
     * Scope a query to search intensities by name or description.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                ->orWhere('description', 'LIKE', "%{$term}%");
        });
    }

    /**
     * Scope a query to order intensities by calorie multiplier.
     */
    public function scopeOrderByMultiplier(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('calorie_multiplier', $direction);
    }

    /**
     * Scope a query to get intensities created after a specific date.
     */
    public function scopeCreatedAfter(Builder $query, string $date): Builder
    {
        return $query->where('created_at', '>', Carbon::parse($date));
    }

    /**
     * Get the formatted calorie multiplier.
     */
    public function getFormattedMultiplierAttribute(): string
    {
        return number_format($this->calorie_multiplier, 2) . 'x';
    }

    /**
     * Get the status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get total exercise logs count for this intensity.
     */
    public function getTotalLogsCountAttribute(): int
    {
        return $this->exerciseLogs()->count();
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Order by sort_order by default
        
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'id';
    }

    /**
     * Determine if the intensity level is high intensity.
     */
    public function isHighIntensity(): bool
    {
        return $this->calorie_multiplier >= 1.5;
    }

    /**
     * Calculate calories burned for a given base calorie amount.
     */
    public function calculateCaloriesBurned(float $baseCalories): float
    {
        return $baseCalories * $this->calorie_multiplier;
    }
}
