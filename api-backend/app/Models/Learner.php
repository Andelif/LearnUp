<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    protected $primaryKey = 'LearnerID'; // This is UserID (FK)
    public $incrementing = false; // Since it's a FK from User

    protected $fillable = [
        'LearnerID', 'full_name', 'guardian_full_name', 'contact_number', 
        'guardian_contact_number', 'gender', 'address'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'LearnerID');
    }

    public function tuitionRequests()
    {
        return $this->hasMany(TuitionRequest::class, 'learner_id');
    }
}
