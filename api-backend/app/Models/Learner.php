<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    protected $table = 'learners';
    protected $primaryKey = 'LearnerID';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true; // uses created_at, updated_at

    protected $fillable = [
        'user_id',
        'full_name',
        'guardian_full_name',
        'contact_number',
        'guardian_contact_number',
        'gender',
        'address',
    ];
}
