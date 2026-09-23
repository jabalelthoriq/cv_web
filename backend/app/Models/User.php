<?php

namespace App\Models;

use App\Models\Cv;
use App\Models\Interview;
use App\Models\JobRecommendation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Subscription;

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
        'avatar',
        'google_id',
        'email_verified_at',
        'last_login_at',
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
            'last_login_at' => 'datetime',
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
    public function subscription()
{
    return $this->hasOne(Subscription::class)->latestOfMany();
}
public function hasActiveSubscription()
{
    return \App\Models\Subscription::where('user_id', $this->id)
        ->where('status', 'active')
        ->where('expired_at', '>', now())
        ->exists();
}

public function currentPlan(): string
{
    if ($this->role === 'admin') {
        return 'pro';
    }

    return $this->activeSubscription()->value('plan') ?? 'free';
}

public function hasPlanAtLeast(string $requiredPlan): bool
{
    $rank = [
        'free' => 0,
        'plus' => 1,
        'pro' => 2,
    ];

    return ($rank[$this->currentPlan()] ?? 0) >= ($rank[$requiredPlan] ?? 0);
}
public function activeSubscription()
{
    return $this->hasOne(Subscription::class)
        ->where('status', 'active')
        ->where('expired_at', '>', now())
        ->latestOfMany();
}

public function payments()
{
    return $this->hasMany(\App\Models\Payment::class);
}
}
