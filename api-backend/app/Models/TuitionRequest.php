<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TuitionRequest extends Model
{
    use HasFactory;

    protected $table = 'tuition_requests';

    // Your table uses TutionID as PK
    protected $primaryKey = 'TutionID';
    public $incrementing = true;

    // If the table doesn't have created_at/updated_at, keep false:
    public $timestamps = false;

    protected $fillable = [
        'LearnerID',
        'class',
        'subjects',
        'asked_salary',
        'curriculum',
        'days',
        'location',
    ];
}
