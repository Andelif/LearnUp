<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TuitionRequest extends Model
{
    use HasFactory;

    protected $table = 'tuition_requests';
    protected $primaryKey = 'TutionID';
    public $incrementing = true;

    /** created_at / updated_at EXIST in your table */
    public $timestamps = true;

    protected $fillable = [
        'LearnerID',
        'class',
        'subjects',
        'asked_salary',
        'curriculum',
        'days',
        'location',
    ];

    protected $casts = [
        'asked_salary' => 'integer',
    ];

    /** Belongs to Learner via non-standard PK */
    public function learner()
    {
        return $this->belongsTo(Learner::class, 'LearnerID', 'LearnerID');
    }

    /** Applications referencing this request (applications.tution_id -> tuition_requests.TutionID) */
    public function applications()
    {
        return $this->hasMany(Application::class, 'tution_id', 'TutionID');
    }
}
