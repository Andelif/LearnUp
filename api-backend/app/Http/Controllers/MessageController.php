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

    // Fetch matched users for messaging
    public function getMatchedUsers()
    {
        $user = Auth::user();


        $userRole = DB::select("SELECT role FROM users WHERE id = ?", 
            [$user->id]);
            
        $role = $userRole[0]->role;    
               


        if($role === "tutor")
        {
            
            $matchedUsers = DB::select("SELECT l.user_id, l.full_name, 'learner' AS role, a.tution_id
                                        FROM learners l JOIN applications a ON l.LearnerID = a.learner_id 
                                            WHERE a.matched = true AND a.tutor_id = 
                                                (SELECT TutorID FROM tutors WHERE user_id = ?)"
                                                , [ $user->id]);

            
        }elseif($role === "learner")
        {
            
            $matchedUsers = DB::select("SELECT t.user_id, t.full_name, 'tutor' AS role, a.tution_id
                                        FROM tutors t JOIN applications a ON t.TutorID = a.tutor_id 
                                            WHERE a.matched = true AND a.learner_id = 
                                                (SELECT LearnerID FROM learners WHERE user_id = ?)"
                                                , [ $user->id]);

            
        } else {
            $matchedUsers = []; 
        }

        return response()->json($matchedUsers);

    }

    // Reject Tutor API
    public function rejectTutor(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tutor_id' => 'required|exists:tutors,user_id',
            'tution_id' => 'required|exists:applications,tution_id',
        ]);

        // Ensure the user is a learner
        $learner = DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$user->id]);

        if (empty($learner)) {
            return response()->json(['error' => 'Only learners can reject tutors'], 403);
        }

        $learnerId = $learner[0]->LearnerID;

        // Update application status
        DB::update("UPDATE applications SET status = 'Cancelled' WHERE tutor_id = ? AND learner_id = ? AND tution_id = ?", [
            $request->tutor_id,
            $learnerId,
            $request->tution_id
        ]);

        return response()->json(['message' => 'Tutor rejected successfully.']);
    }

}