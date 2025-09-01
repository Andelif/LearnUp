<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $table = 'applications';
    protected $primaryKey = 'ApplicationID';
    public $incrementing = true;

    /** created_at / updated_at EXIST */
    public $timestamps = true;

    protected $fillable = [
        'tution_id',     // FK -> tuition_requests.TutionID  (note: tution*)
        'learner_id',    // FK -> learners.LearnerID
        'tutor_id',      // FK -> tutors.TutorID
        'matched',
        'status',
        'payment_status',
    ];

    protected $casts = [
        'matched' => 'boolean',
    ];

    public function tuitionRequest()
    {
        return $this->belongsTo(TuitionRequest::class, 'tution_id', 'TutionID');
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id', 'LearnerID');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'TutorID');
    }

    /** Convenience: confirmed tuition (if any) */
    public function confirmedTuition()
    {
        return $this->hasOne(ConfirmedTuition::class, 'application_id', 'ApplicationID');
    }
}
