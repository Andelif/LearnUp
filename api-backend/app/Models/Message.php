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

    /** No created_at/updated_at; table uses TimeStamp column */
    public $timestamps = false;

    protected $fillable = [
        'SentBy',
        'SentTo',
        'Content',
        'Status',     // 'Delivered' | 'Seen'
        'TimeStamp',
    ];

    protected $casts = [
        'TimeStamp' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'SentBy', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'SentTo', 'id');
    }
}
