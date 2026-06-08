<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $fillable = [
        'user_id', 'uuid', 'name', 'species', 'breed', 'gender', 'birth_date', 'coat_color', 'photo_path', 'emergency_contact', 'medical_conditions'
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function weights()
    {
        return $this->hasMany(Weight::class)->orderBy('recorded_at', 'desc');
    }

    public function vaccines()
    {
        return $this->hasMany(Vaccine::class)->orderBy('date_given', 'desc');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class)->orderBy('date_performed', 'desc');
    }

    public function observations()
    {
        return $this->hasMany(Observation::class)->orderBy('date_observed', 'desc');
    }
}
