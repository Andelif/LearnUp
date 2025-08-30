<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    /**
     * Return notifications for the current user:
     *  - personal: user_id == current user
     *  - broadcasts: view in ('everyone', role-specific)
     */
    public function getUserNotifications()
    {
        $user = Auth::user();
        if (!$user) {
            return ['error' => 'Unauthorized', 'status' => 403];
        }

        $roleView = match ($user->role) {
            'learner' => 'all_learner',
            'tutor'   => 'all_tutor',
            'admin'   => 'all_admin', // optional; will match nothing if you don't use it
            default   => null,
        };

        $rows = DB::table('notifications')
            ->where(function ($q) use ($user, $roleView) {
                $q->where('user_id', $user->id)               // personal
                  ->orWhere('view', 'everyone');              // broadcasts to all

                if ($roleView) {
                    $q->orWhere('view', $roleView);           // broadcasts to role
                }
            })
            ->orderByDesc('TimeSent')
            ->orderByDesc('NotificationID')
            ->get();

        return $rows;
    }

    /**
     * Mark a personal notification as read.
     * (We don't flip broadcast rows globally.)
     */
    public function markNotificationAsRead($id)
    {
        $user = Auth::user();
        if (!$user) {
            return ['error' => 'Unauthorized', 'status' => 403];
        }

        $row = DB::table('notifications')->where('NotificationID', $id)->first();
        if (!$row) {
            return ['error' => 'Notification not found', 'status' => 404];
        }

        // Only allow marking as read if this notification is addressed to the user
        if ((int)($row->user_id ?? 0) !== (int)$user->id) {
            return ['error' => 'Cannot mark this notification as read', 'status' => 403];
        }

        DB::table('notifications')
            ->where('NotificationID', $id)
            ->update(['Status' => 'Read']);

        return ['message' => 'Notification marked as read', 'status' => 200];
    }

    /**
     * Create a notification.
     * If you pass a 'view' of 'everyone'|'all_learner'|'all_tutor'|('all_admin'),
     * it will be treated as a broadcast. Otherwise it's personal (to user_id).
     */
    public function createNotification(array $data)
    {
        $payload = [
            'user_id' => $data['user_id'] ?? null, // recipient for personal; can be null for broadcast if schema allows
            'Message' => $data['Message'],
            'Type'    => $data['Type'],
            'Status'  => 'Unread',
            'TimeSent'=> now(),
            'view'    => $data['view'] ?? null,
        ];

        $notif = Notification::create($payload);

        return ['message' => 'Notification created', 'data' => $notif, 'status' => 201];
    }
}
