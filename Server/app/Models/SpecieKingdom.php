<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpecieKingdom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function species()
    {
        return $this->hasMany(Specie::class);
    }
}
