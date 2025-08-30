<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutor extends Model
{
    use HasFactory;

    protected $table = 'tutors';

    // If your table doesn't have created_at/updated_at, keep this false.
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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
