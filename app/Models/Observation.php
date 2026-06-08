<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Observation extends Model
{
    protected $fillable = ['pet_id', 'title', 'description', 'date_observed'];

    protected $casts = [
        'date_observed' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
