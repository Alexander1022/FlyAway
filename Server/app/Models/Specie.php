<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specie extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'specie_kingdom_id',
        'habitat_id',
        'common_name',
        'scientific_name',
    ];

    public function specieTypes()
    {
        return $this->belongsToMany(SpecieType::class, 'specie_specie_type');
    }

    public function specieKingdom()
    {
        return $this->belongsTo(SpecieKingdom::class);
    }

    public function habitat()
    {
        return $this->belongsTo(Habitat::class);
    }

    public function image()
    {
        return $this->morphOne(FileRecord::class, 'fileable');
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
