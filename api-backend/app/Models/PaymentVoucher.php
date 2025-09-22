<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentVoucher extends Model
{
    protected $table = 'payment_vouchers';
    protected $primaryKey = 'PaymentVoucherID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;

    protected $fillable = [
        'tution_id',
        'tutor_id',
        'learner_id',
        'total_salary',
        'media_fee_amount',
        'tutor_amount',
        'voucher_id',
        'status',
        'payment_method',
        'transaction_id',
        'payment_notes',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id', 'TutorID');
    }

    public function learner()
    {
        return $this->belongsTo(Learner::class, 'learner_id', 'LearnerID');
    }

    public function tution()
    {
        return $this->belongsTo(ConfirmedTuition::class, 'tution_id', 'tution_id');
    }
}
