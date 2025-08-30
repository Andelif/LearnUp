<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $primaryKey = 'MessageID';
    public $incrementing = true;
    public $timestamps = false; // uses TimeStamp column instead

    protected $fillable = [
        'SentBy',
        'SentTo',
        'Content',
        'Status',     // e.g., 'Delivered' | 'Seen'
        'TimeStamp',  // datetime
    ];

    protected $casts = [
        'TimeStamp' => 'datetime',
    ];
}
