<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Weight extends Model
{
    protected $fillable = [
        'pet_id', 'weight', 'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'date',
        'weight' => 'decimal:2',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
