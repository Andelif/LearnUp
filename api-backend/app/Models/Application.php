<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';

    // Your table uses ApplicationID as PK
    protected $primaryKey = 'ApplicationID';
    public $incrementing = true;

    // If the table doesn't have created_at/updated_at, keep false:
    public $timestamps = false;

    protected $fillable = [
        'tution_id',   // references tuition_requests.TutionID (note: tution_id)
        'learner_id',  // references learners.LearnerID
        'tutor_id',    // references tutors.TutorID
        'matched',
        'status',      // e.g., Applied, Shortlisted, Confirmed, Cancelled
    ];

    protected $casts = [
        'matched' => 'boolean',
    ];
}
