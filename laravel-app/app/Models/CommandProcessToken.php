<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandProcessToken extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        'command_id',
        'token',
        'comment',
        'created_at',
    ];

    public function command()
    {
        return $this->belongsTo(Command::class);
    }
}
