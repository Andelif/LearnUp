<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutor extends Model
{
    use HasFactory;

    protected $table = 'tutors';
    protected $primaryKey = 'TutorID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'contact_number',
        'gender',
        'preferred_salary',
        'qualification',
        'experience',
        'currently_studying_in',
        'preferred_location',
        'preferred_time',
        'availability',
        'profile_picture',   
    ];

    protected $casts = [
        'preferred_salary' => 'integer',
        'availability'     => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
