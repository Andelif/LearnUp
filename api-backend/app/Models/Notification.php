<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    // Your table uses NotificationID as PK
    protected $primaryKey = 'NotificationID';
    public $incrementing = true;

    // If the table doesn't have created_at/updated_at, keep false:
    public $timestamps = false;

    protected $fillable = [
        'user_id',   // recipient user id (nullable for pure broadcasts if your schema allows null)
        'TimeSent',
        'Message',
        'Type',
        'Status',    // 'Unread' | 'Read'
        'view',      // 'everyone' | 'all_learner' | 'all_tutor' | (optional 'all_admin') | null
    ];

    protected $casts = [
        'TimeSent' => 'datetime',
    ];
}
