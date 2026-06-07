<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'pet_id', 'name', 'date_performed', 'file_path', 'notes'
    ];

    protected $casts = [
        'date_performed' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }
}
