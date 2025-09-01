<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfirmedTuition extends Model
{
    use HasFactory;

    /** Your actual table is snake_case: confirmed_tuitions */
    protected $table = 'confirmed_tuitions';
    protected $primaryKey = 'ConfirmedTuitionID';
    public $incrementing = true;

    /** No created_at/updated_at in this table */
    public $timestamps = false;

    protected $fillable = [
        'application_id',   // -> applications.ApplicationID
        'tution_id',        // -> tuition_requests.TutionID
        'FinalizedSalary',  // numeric(10,2)
        'FinalizedDays',    // varchar(255)
        'Status',           // 'Ongoing' | 'Ended'
        'ConfirmedDate',    // timestamptz
    ];

    protected $casts = [
        'FinalizedSalary' => 'decimal:2',
        'ConfirmedDate'   => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class, 'application_id', 'ApplicationID');
    }

    public function tuitionRequest()
    {
        return $this->belongsTo(TuitionRequest::class, 'tution_id', 'TutionID');
    }
}
