<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'plan_id',
        'user_id',
        'rating',
    ];

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
    
}
