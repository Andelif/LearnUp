<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /** Table & PK are Laravel defaults: users.id (bigint, increments) */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** Roles (convenience) */
    public function isLearner(): bool { return $this->role === 'learner'; }
    public function isTutor(): bool   { return $this->role === 'tutor'; }
    public function isAdmin(): bool   { return $this->role === 'admin'; }

    /** === Relationships === */

    /** One-to-one to learners by FK learners.user_id -> users.id */
    public function learner()
    {
        return $this->hasOne(Learner::class, 'user_id', 'id');
    }

    /** One-to-one to tutors by FK tutors.user_id -> users.id */
    public function tutor()
    {
        return $this->hasOne(Tutor::class, 'user_id', 'id');
    }

    /**
     * User -> Learner -> TuitionRequest (hasManyThrough with custom keys)
     * users.id -> learners.user_id
     * learners.LearnerID -> tuition_requests.LearnerID
     */
    public function tuitionRequests()
    {
        return $this->hasManyThrough(
            TuitionRequest::class,  // final
            Learner::class,         // through
            'user_id',              // firstKey (on learners referencing users)
            'LearnerID',            // secondKey (on tuition_requests referencing learners)
            'id',                   // localKey (on users)
            'LearnerID'             // secondLocalKey (PK on learners)
        );
    }

    /**
     * Applications as LEARNER via learners.LearnerID -> applications.learner_id
     */
    public function applicationsAsLearner()
    {
        return $this->hasManyThrough(
            Application::class,
            Learner::class,
            'user_id',
            'learner_id',
            'id',
            'LearnerID'
        );
    }

    /**
     * Applications as TUTOR via tutors.TutorID -> applications.tutor_id
     */
    public function applicationsAsTutor()
    {
        return $this->hasManyThrough(
            Application::class,
            Tutor::class,
            'user_id',
            'tutor_id',
            'id',
            'TutorID'
        );
    }

    /** Messages & notifications (SentBy/SentTo are user ids in your schema) */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'SentBy', 'id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'SentTo', 'id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id', 'id');
    }
}
