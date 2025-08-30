<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;

    protected $table = 'admins';

    // If your admins table doesn't have created_at/updated_at, keep false:
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'full_name',
        'address',
        'contact_number',
        'permission_req',
        'match_made',
        'task_assigned',
    ];

    protected $casts = [
        'permission_req' => 'boolean',
        'match_made'     => 'integer',
        'task_assigned'  => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
