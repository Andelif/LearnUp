<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    use HasFactory;

    protected $primaryKey = 'AdminID'; // This is UserID (FK)
    public $incrementing = false;

    protected $fillable = [
        'AdminID', 'full_name', 'address', 'contact_number', 
        'permission_req', 'match_made', 'task_assigned'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'AdminID');
    }
}
