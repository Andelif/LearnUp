<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationService{
    public function getUserNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return ['error' => 'Unauthorized', 'status' => 403];
        }

        $notifications = DB::select(
            "SELECT * FROM notifications 
            WHERE view = 'everyone' 
            OR (view = 'all_learner' AND EXISTS (SELECT 1 FROM learners WHERE user_id = ?))
            OR (view = 'all_tutor' AND EXISTS (SELECT 1 FROM tutors WHERE user_id = ?))
            OR view = ?
            ORDER BY TimeSent DESC",
            [$user->id, $user->id, $user->id]
        );

        return $notifications;
    }

    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        $notification = DB::select("SELECT * FROM notifications WHERE NotificationID = ?", [$id]);

        if (!$notification) {
            return ['error' => 'Notification not found or unauthorized', 'status' => 404];
        }

        DB::update("UPDATE notifications SET Status = 'Read' WHERE NotificationID = ?", [$id]);
        return ['message' => 'Notification marked as read', 'status' => 200];
    }

    public function createNotification($data)
    {
        DB::insert("INSERT INTO notifications (user_id, Message, Type, Status, TimeSent, view) VALUES (?, ?, ?, 'Unread', NOW(), ?)", [
            $data['user_id'],
            $data['Message'],
            $data['Type'],
            $data['view']
        ]);

        return ['message' => 'Notification created', 'status' => 201];
    }
}