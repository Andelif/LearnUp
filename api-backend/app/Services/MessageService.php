<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    public function sendMessage($sentTo, $content)
    {
        // Logic to send the message to the recipient
        // Assuming a `messages` table to save the message
        DB::table('messages')->insert([
            'sent_to' => $sentTo,
            'content' => $content,
            'sent_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return ['message' => 'Message sent successfully!'];
    }

    public function getMessages($userId)
    {
        // Fetch messages between the authenticated user and the provided user
        $messages = DB::select("
            SELECT m.*, u.full_name AS sender_name
            FROM messages m
            JOIN users u ON m.sent_by = u.id
            WHERE (m.sent_by = ? AND m.sent_to = ?) OR (m.sent_by = ? AND m.sent_to = ?)
            ORDER BY m.created_at ASC
        ", [Auth::id(), $userId, $userId, Auth::id()]);

        return $messages;
    }

    public function markAsSeen($senderId)
    {
        // Mark the messages as seen for the current user and the provided sender
        DB::table('messages')
            ->where('sent_by', $senderId)
            ->where('sent_to', Auth::id())
            ->update(['seen' => true]);

        return ['message' => 'Messages marked as seen'];
    }

    public function getMatchedUsers()
    {
        $user = Auth::user();
        $userRole = DB::select("SELECT role FROM users WHERE id = ?", [$user->id]);
        $role = $userRole[0]->role;

        if ($role === 'tutor') {
            $matchedUsers = DB::select("
                SELECT l.user_id, l.full_name, 'learner' AS role, a.tution_id, a.ApplicationID
                FROM learners l
                JOIN applications a ON l.LearnerID = a.learner_id
                WHERE a.matched = true AND a.tutor_id = (SELECT TutorID FROM tutors WHERE user_id = ?)
            ", [$user->id]);
        } elseif ($role === 'learner') {
            $matchedUsers = DB::select("
                SELECT t.user_id, t.full_name, 'tutor' AS role, a.tution_id, a.ApplicationID
                FROM tutors t
                JOIN applications a ON t.TutorID = a.tutor_id
                WHERE a.matched = true AND a.learner_id = (SELECT LearnerID FROM learners WHERE user_id = ?)
            ", [$user->id]);
        } else {
            $matchedUsers = [];
        }

        return $matchedUsers;
    }

    public function rejectTutor($tutorId, $tutionId)
    {
        // Logic to reject a tutor
        $updateResponse = DB::table('applications')
            ->where('tutor_id', $tutorId)
            ->where('tution_id', $tutionId)
            ->update(['status' => 'Rejected']);

        if ($updateResponse) {
            return ['message' => 'Tutor rejected successfully'];
        }

        return ['error' => 'Unable to reject tutor'];
    }
}
