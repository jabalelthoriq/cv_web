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
    'result_link'
];
    public function cv()
{
    return $this->belongsTo(Cv::class);
}
}
