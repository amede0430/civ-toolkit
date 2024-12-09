<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être remplis en masse.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
}
