<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agreement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'title',
        'content',
        'version',
        'is_active',
        'effective_date',
        'summary'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'effective_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    public function userAgreements(): HasMany
    {
        return $this->hasMany(UserAgreement::class, 'agreement_id', 'id');
    }
}