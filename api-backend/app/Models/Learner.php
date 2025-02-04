<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    use HasFactory;

    protected $primaryKey = 'LearnerID';
    public $incrementing = true; 

    protected $fillable = [
        'user_id', 'full_name', 'guardian_full_name', 'contact_number', 
        'guardian_contact_number', 'gender', 'address'
    ];

    // Foreign key relationship with User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function tuitionRequests()
    {
        return $this->hasMany(TuitionRequest::class, 'learner_id');
    }
}
