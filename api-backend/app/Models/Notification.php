<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'NotificationID';
    public $incrementing = true;

    /** created_at / updated_at EXIST */
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'TimeSent',
        'Message',
        'Type',
        'Status',  // 'Unread' | 'Read'
        'view',    // 'everyone' | 'all_learner' | 'all_tutor' | (maybe 'all_admin') | null
    ];

    protected $casts = [
        'TimeSent' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
