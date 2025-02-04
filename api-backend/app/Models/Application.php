<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $primaryKey = 'ApplicationID';

    protected $fillable = [
        'tution_id', 'learner_id', 'tutor_id'
    ];

    public function tuitionRequest()
    {
        return $this->belongsTo(TuitionRequest::class, 'tution_id');
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id');
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }
}
