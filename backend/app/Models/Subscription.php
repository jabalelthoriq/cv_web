<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $table = 'subscriptions';

    protected $fillable = [
        'user_id',
        'plan',
        'started_at',
        'expired_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS
    |--------------------------------------------------------------------------
    */

    // Cek apakah subscription masih aktif
    public function isActive()
    {
        return $this->status === 'active' &&
               ($this->expired_at === null || $this->expired_at->isFuture());
    }

    // Cek apakah sudah expired
    public function isExpired()
    {
        return $this->expired_at !== null &&
               $this->expired_at->isPast();
    }

    // Cek plan
    public function isPro()
    {
        return $this->plan === 'pro' && $this->isActive();
    }

    public function isPlus()
    {
        return $this->plan === 'plus' && $this->isActive();
    }

    public function isFree()
    {
        return $this->plan === 'free';
    }

    // Sisa hari subscription
    public function daysRemaining()
    {
        if (!$this->expired_at) return null;

        return now()->diffInDays($this->expired_at, false);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saving(function ($subscription) {
            if ($subscription->expired_at && $subscription->expired_at->isPast()) {
                $subscription->status = 'expired';
            }
        });
    }
}