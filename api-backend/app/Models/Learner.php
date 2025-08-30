<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Learner extends Model
{
    use HasFactory;

    protected $table = 'learners';

    // If your table does NOT have created_at/updated_at, keep this false.
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'guardian_full_name',
        'contact_number',
        'guardian_contact_number',
        'gender',
        'address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
