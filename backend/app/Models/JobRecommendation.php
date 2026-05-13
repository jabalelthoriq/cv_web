<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobRecommendation extends Model
{
    use HasFactory; 

    protected $fillable = [
        'user_id',
        'cv_id',
        'job_title',
        'company',
        'match_score',
        'job_link',
        'job_tema'  // tambahkan ini
    ];

    // Relasi ke user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}