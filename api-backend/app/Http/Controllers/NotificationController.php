<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $notifications = DB::select("SELECT * FROM notifications WHERE user_id = ? ORDER BY TimeSent DESC", [$user->id]);
        return response()->json($notifications);
    }

    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = DB::select("SELECT * FROM notifications WHERE id = ? AND user_id = ?", [$id, $user->id]);

        if (!$notification) {
            return response()->json(['message' => 'Notification not found or unauthorized'], 404);
        }

        DB::update("UPDATE notifications SET Status = 'Read' WHERE id = ?", [$id]);
        return response()->json(['message' => 'Notification marked as read']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'Message' => 'required|string',
            'Type' => 'required|in:Tuition Request,Application Update,New Message,Admin Message,General',
        ]);

        DB::insert("INSERT INTO notifications (user_id, Message, Type, Status, TimeSent) VALUES (?, ?, ?, 'Unread', NOW())", [
            $request->user_id,
            $request->Message,
            $request->Type
        ]);

        return response()->json(['message' => 'Notification created'], 201);
    }
}

