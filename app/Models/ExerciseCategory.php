<?php
// app/Models/ExerciseCategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExerciseCategory extends Model
{
    use HasFactory, HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name',
        'description',
        'measurement_type',
    ];



    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',

    ];

    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class, 'category_id', 'id');
    }
}
