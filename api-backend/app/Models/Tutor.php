<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $primaryKey = 'TutorID'; 
    public $incrementing = true; 

    protected $fillable = [
        'user_id', 'full_name', 'address', 'contact_number', 'gender',
        'preferred_salary', 'qualification', 'experience', 'currently_studying_in',
        'preferred_location', 'preferred_time', 'availability'
    ];

    // Foreign key relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'tutor_id');
    }
}
