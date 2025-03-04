<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageService
{
    public function sendMessage($sentTo, $content)
    {
        $user = Auth::user();

        DB::insert("INSERT INTO messages (SentBy, SentTo, Content) VALUES (?, ?, ?)", [
            $user->id,
            $sentTo,
            $content
        ]);

        return ['message' => 'Message sent successfully'];
    }

    public function getMessages($userId)
    {
        $user = Auth::user();

        return DB::select("
            SELECT * FROM messages 
            WHERE (SentBy = ? AND SentTo = ?) OR (SentBy = ? AND SentTo = ?)
            ORDER BY TimeStamp ASC", 
            [$user->id, $userId, $userId, $user->id]
        );
    }

    public function markAsSeen($senderId)
    {
        $user = Auth::user();

        DB::update("UPDATE messages SET Status = 'Seen' WHERE SentBy = ? AND SentTo = ? AND Status = 'Delivered'", [
            $senderId, $user->id
        ]);

        return ['message' => 'Messages marked as seen'];
    }

    public function getMatchedUsers()
    {
        $user = Auth::user();
        $roleData = DB::select("SELECT role FROM users WHERE id = ?", [$user->id]);
        
        if (empty($roleData)) {
            return [];
        }

        $role = $roleData[0]->role;

        if ($role === "tutor") {
            return DB::select("
                SELECT l.user_id, l.full_name, 'learner' AS role, a.tution_id
                FROM learners l 
                JOIN applications a ON l.LearnerID = a.learner_id 
                WHERE a.matched = true AND a.tutor_id = 
                    (SELECT TutorID FROM tutors WHERE user_id = ?)", 
                [$user->id]
            );
        } elseif ($role === "learner") {
            return DB::select("
                SELECT t.user_id, t.full_name, 'tutor' AS role, a.tution_id
                FROM tutors t 
                JOIN applications a ON t.TutorID = a.tutor_id 
                WHERE a.matched = true AND a.learner_id = 
                    (SELECT LearnerID FROM learners WHERE user_id = ?)", 
                [$user->id]
            );
        }

        return [];
    }

    public function rejectTutor($tutorId, $tutionId)
    {
        $user = Auth::user();
        $learner = DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$user->id]);

        if (empty($learner)) {
            return ['error' => 'Only learners can reject tutors'];
        }

        $learnerId = $learner[0]->LearnerID;

        DB::update("UPDATE applications SET status = 'Cancelled' WHERE tutor_id = ? AND learner_id = ? AND tution_id = ?", [
            $tutorId,
            $learnerId,
            $tutionId
        ]);

        return ['message' => 'Tutor rejected successfully.'];
    }
}
