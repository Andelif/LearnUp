<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $primaryKey = 'AdminID'; 
    public $incrementing = true;

    protected $fillable = [
        'user_id', 'full_name', 'address', 'contact_number', 
        'permission_req', 'match_made', 'task_assigned'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
