<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $primaryKey = 'TutorID'; // This is UserID (FK)
    public $incrementing = false;

    protected $fillable = [
        'TutorID', 'full_name', 'address', 'contact_number', 'gender',
        'preferred_salary', 'qualification', 'experience', 'currently_studying_in',
        'preferred_location', 'preferred_time', 'availability'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'TutorID');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'tutor_id');
    }
}
