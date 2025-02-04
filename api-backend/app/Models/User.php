<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Define relationships
    public function learner()
    {
        return $this->hasOne(\App\Models\Learner::class);
    }

    public function tutor()
    {
        return $this->hasOne(\App\Models\Tutor::class);
    }

    public function tuitionRequests()
    {
        return $this->hasMany(\App\Models\TuitionRequest::class, 'learner_id');
    }

    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'tutor_id');
    }

    // Role-based helper methods
    public function isLearner()
    {
        return $this->role === 'learner';
    }

    public function isTutor()
    {
        return $this->role === 'tutor';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}
