<?php

namespace App\Models;

use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobRecommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Mass assignable
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'avatar'
    ];

    /**
     * Hidden fields
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ─────────────────────────────
    // RELATIONSHIPS
    // ─────────────────────────────

    /**
     * 1 user → banyak CV
     */
    public function cvs()
    {
        return $this->hasMany(Cv::class);
    }

    /**
     * 1 user → banyak job recommendation
     */
    public function jobRecommendations()
    {
        return $this->hasMany(JobRecommendation::class);
    }

    /**
     * 1 user → banyak interview
     */
    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }
}