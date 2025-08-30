<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfirmedTuition extends Model
{
    use HasFactory;

    // If your table name is snake_case (recommended), change this to 'confirmed_tuitions'.
    protected $table = 'ConfirmedTuitions';

    // Your PK appears to be ConfirmedTuitionID
    protected $primaryKey = 'ConfirmedTuitionID';
    public $incrementing = true;

    // If the table doesn't have created_at/updated_at:
    public $timestamps = false;

    protected $fillable = [
        'application_id',  // -> applications.ApplicationID
        'tution_id',       // -> tuition_requests.TutionID  (note: tution)
        'FinalizedSalary',
        'FinalizedDays',
        'Status',          // 'Ongoing' | 'Ended'
    ];

    protected $casts = [
        'FinalizedSalary' => 'float',
    ];
}
