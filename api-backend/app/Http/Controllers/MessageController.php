<?php

namespace App\Http\Controllers;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    protected $messageService;

    public function __construct(MessageService $messageService)
    {
        $this->messageService = $messageService;
    }
    public function sendMessage(Request $request)
    {
        $request->validate([
            'SentTo' => 'required|exists:users,ID',
            'Content' => 'required|string|max:2000',
        ]);

        $response = $this->messageService->sendMessage($request->SentTo, $request->Content);
        return response()->json($response, 201);
    }

    // Get messages between the authenticated user and another user
    public function getMessages($userId)
    {
        $messages = $this->messageService->getMessages($userId);
        return response()->json($messages);
    }

    // Mark messages as seen
    public function markAsSeen($senderId)
    {
        $response = $this->messageService->markAsSeen($senderId);
        return response()->json($response);
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
            
            $matchedUsers = DB::select("SELECT l.user_id, l.full_name, 'learner' AS role, a.tution_id, a.ApplicationID
                                        FROM learners l JOIN applications a ON l.LearnerID = a.learner_id 
                                            WHERE a.matched = true AND a.tutor_id = 
                                                (SELECT TutorID FROM tutors WHERE user_id = ?)"
                                                , [ $user->id]);

            
        }elseif($role === "learner")
        {
            
            $matchedUsers = DB::select("SELECT t.user_id, t.full_name, 'tutor' AS role, a.tution_id, a.ApplicationID
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
        $request->validate([
            'tutor_id' => 'required|exists:tutors,user_id',
            'tution_id' => 'required|exists:applications,tution_id',
        ]);

        $response = $this->messageService->rejectTutor($request->tutor_id, $request->tution_id);

        if (isset($response['error'])) {
            return response()->json($response, 403);
        }

        return response()->json($response);
    }

}