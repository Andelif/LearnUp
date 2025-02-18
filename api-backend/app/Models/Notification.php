<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Notification extends Model
{
    protected $primaryKey = 'NotificationID';
    public $incrementing = true;

    public static function createNotification($data)
    {
        DB::insert("INSERT INTO notifications (user_id, TimeSent, Message, Type, Status) VALUES (?, ?, ?, ?, ?)", [
            $data['user_id'],
            $data['TimeSent'],
            $data['Message'],
            $data['Type'],
            $data['Status']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    public static function findByUserId($user_id)
    {
        return DB::select("SELECT * FROM notifications WHERE user_id = ?", [$user_id]);
    }
}
