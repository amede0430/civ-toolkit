<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'plan_id',
        'user_id',
        'comment',
    ];

    public function plan() {
        return $this->belongsTo(Plan::class);
    }
}
