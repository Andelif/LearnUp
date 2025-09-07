<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConfirmedTuition extends Model
{
    use HasFactory;

    // Correct snake_case table for Postgres
    protected $table = 'confirmed_tuitions';
    protected $primaryKey = 'ConfirmedTuitionID';
    public $incrementing = true;
    public $timestamps = false; // no created_at/updated_at columns

    protected $fillable = [
        'application_id',   // applications.ApplicationID
        'tution_id',        // tuition_requests.TutionID  (note: "tution" per schema)
        'FinalizedSalary',
        'FinalizedDays',
        'Status',           // 'Ongoing' | 'Ended'
        'ConfirmedDate',
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
