<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
    'order_id',
    'user_id',
    'cv_id',
    'type',
    'plan',
    'status',
    'result_link',
    'paid_at',
];
    public function cv()
{
    return $this->belongsTo(Cv::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
}
