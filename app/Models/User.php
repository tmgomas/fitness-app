<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */

    use HasApiTokens, HasFactory, Notifiable;
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
        'is_admin',
        'gender',
        'birthday'
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_admin' => 'boolean',
        'birthday' => 'date',  // Add this line
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = static::generateUniqueUsername($user->name);
            }
            if (empty($user->password)) {
                $user->password = static::generateRandomPassword();
            }
        });
    }

    private static function generateUniqueUsername($name)
    {
        $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name));
        $username = $baseUsername;
        $counter = 1;

        while (static::where('username', $username)->exists()) {
            $username = $baseUsername . $counter;
            $counter++;
        }

        return $username;
    }

    private static function generateRandomPassword()
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return $this->is_admin === true;
    }
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */


    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Add these relationship methods to the User model

    /**
     * Get the health data records for the user.
     */
    public function healthData()
    {
        return $this->hasMany(UserHealthData::class, 'user_id', 'id');
    }

    /**
     * Get the preferences for the user.
     */
    public function preferences()
    {
        return $this->hasMany(UserPreference::class, 'user_id', 'id');
    }

    /**
     * Get the measurements for the user.
     */
    public function measurements()
    {
        return $this->hasMany(UserMeasurement::class, 'user_id', 'id');
    }

    /**
     * Get the food logs for the user.
     */
    public function foodLogs()
    {
        return $this->hasMany(UserFoodLog::class, 'user_id', 'id');
    }

    /**
     * Get the exercise logs for the user.
     */
    public function exerciseLogs()
    {
        return $this->hasMany(UserExerciseLog::class, 'user_id', 'id');
    }
    
    public function userAgreements()
{
    return $this->hasMany(UserAgreement::class, 'user_id', 'id');
}

public function acceptedAgreements()
{
    return $this->belongsToMany(Agreement::class, 'user_agreements', 'user_id', 'agreement_id')
        ->withPivot('accepted_at', 'ip_address', 'user_agent')
        ->withTimestamps();
}
}
