<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TuitionRequest extends Model
{
    use HasFactory;

    protected $primaryKey = 'TutionID';

    protected $fillable = [
        'learner_id', 'class', 'subjects', 'asked_salary', 'curriculum', 'days'
    ];

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'tution_id');
    }
}
