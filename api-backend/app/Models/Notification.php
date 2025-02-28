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
        DB::insert("INSERT INTO notifications (user_id, TimeSent, Message, Type, Status, view) VALUES (?, ?, ?, ?, ?, ?)", [
            $data['user_id'],
            $data['TimeSent'],
            $data['Message'],
            $data['Type'],
            $data['Status'],
            $data['view'] 
        ]);
        return DB::getPdo()->lastInsertId();
    }

    public static function findByUserId($user_id)
    {
        return DB::select("SELECT * FROM notifications WHERE view = 'everyone' 
                          OR view = 'all_learner' AND EXISTS (SELECT 1 FROM learners WHERE user_id = ?) 
                          OR view = 'all_tutor' AND EXISTS (SELECT 1 FROM tutors WHERE user_id = ?) 
                          OR view = ? 
                          ORDER BY TimeSent DESC", [$user_id, $user_id, $user_id]);
    }
}
