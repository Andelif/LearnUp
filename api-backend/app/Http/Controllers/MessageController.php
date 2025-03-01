<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    // Send a message
    public function sendMessage(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'SentTo' => 'required|exists:users,ID',
            'Content' => 'required|string|max:2000',
        ]);

        DB::insert("INSERT INTO messages (SentBy, SentTo, Content) VALUES (?, ?, ?)", [
            $user->id,
            $request->SentTo,
            $request->Content
        ]);

        return response()->json(['message' => 'Message sent successfully'], 201);
    }

    // Get messages between the authenticated user and another user
    public function getMessages($userId)
    {
        $user = Auth::user();

        $messages = DB::select("
            SELECT * FROM messages 
            WHERE (SentBy = ? AND SentTo = ?) OR (SentBy = ? AND SentTo = ?)
            ORDER BY TimeStamp ASC", 
            [$user->id, $userId, $userId, $user->id]
        );

        return response()->json($messages);
    }

    // Mark messages as seen
    public function markAsSeen($senderId)
    {
        $user = Auth::user();

        DB::update("UPDATE messages SET Status = 'Seen' WHERE SentBy = ? AND SentTo = ? AND Status = 'Delivered'", [
            $senderId, $user->id
        ]);

        return response()->json(['message' => 'Messages marked as seen']);
    }
}
