<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $primaryKey = 'MessageID';
    public $incrementing = true;
    public $timestamps = false;

    // Send a message
    public static function sendMessage($data)
    {
        DB::insert("INSERT INTO messages (SentBy, SentTo, Content) VALUES (?, ?, ?)", [
            $data['SentBy'],
            $data['SentTo'],
            $data['Content']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    // Get messages between two users
    public static function getMessagesBetween($user1, $user2)
    {
        return DB::select("
            SELECT * FROM messages 
            WHERE (SentBy = ? AND SentTo = ?) OR (SentBy = ? AND SentTo = ?)
            ORDER BY TimeStamp ASC", [$user1, $user2, $user2, $user1]);
    }

    // Mark messages as seen
    public static function markMessagesAsSeen($sender, $receiver)
    {
        DB::update("UPDATE messages SET Status = 'Seen' WHERE SentBy = ? AND SentTo = ? AND Status = 'Delivered'", [
            $sender, $receiver
        ]);
    }
}
