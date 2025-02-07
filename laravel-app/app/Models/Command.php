<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'engineer_id',
        'name',
        'area',
        'levels_number',
        'materials',
        'zone',
        'construction_type',
        'command_type',
        'file_path',
        'deadline',
        'comment',
        'status',
        'price',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commandProcessToken()
    {
        return $this->belongsTo(CommandProcessToken::class);
    }
}
