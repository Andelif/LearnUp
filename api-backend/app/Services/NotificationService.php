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
            'admin'   => 'all_admin',
            default   => null,
        };

        $rows = DB::table('notifications')
            ->where(function ($q) use ($user, $roleView) {
                $q->where('user_id', $user->id)     // personal
                  ->orWhere('view', 'everyone');    // broadcast to all

                if ($roleView) {
                    $q->orWhere('view', $roleView); // broadcast to role
                }
            })
            ->orderByDesc('TimeSent')
            ->orderByDesc('NotificationID')
            ->get();

        return $rows;
    }

    /**
     * Mark a personal notification as read.
     * Broadcast notifications are not per-user state, so we skip those.
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

        // Only personal notifications can be marked as read per user
        if ((int)($row->user_id ?? 0) !== (int)$user->id) {
            return ['error' => 'Cannot mark this notification as read', 'status' => 403];
        }

        DB::table('notifications')
            ->where('NotificationID', $id)
            ->update(['Status' => 'Read', 'updated_at' => now()]);

        return ['message' => 'Notification marked as read', 'status' => 200];
    }

    /**
     * Create a notification.
     * Either user_id (personal) OR view (broadcast: everyone/all_learner/all_tutor/all_admin) must be provided.
     */
    public function createNotification(array $data)
    {
        $isPersonal = !empty($data['user_id']);
        $isBroadcast = !empty($data['view']);

        if (!$isPersonal && !$isBroadcast) {
            return ['error' => 'Provide either user_id (personal) or view (broadcast)', 'status' => 400];
        }

        $payload = [
            'user_id'   => $data['user_id'] ?? null,
            'Message'   => $data['Message'],
            'Type'      => $data['Type'],
            'Status'    => 'Unread',
            'TimeSent'  => now(),
            'view'      => $data['view'] ?? null,
        ];

        $notif = Notification::create($payload);

        return ['message' => 'Notification created', 'data' => $notif, 'status' => 201];
    }
}
