<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamificationStat extends Model
{
    protected $fillable = [
        'total_xp',
        'current_streak',
        'longest_streak',
        'last_active_date',
    ];

    protected $casts = [
        'last_active_date' => 'date',
    ];
}
