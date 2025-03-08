<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
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

    public function getMessages($userId)
    {
        $messages = $this->messageService->getMessages($userId);
        return response()->json($messages);
    }

    public function markAsSeen($senderId)
    {
        $response = $this->messageService->markAsSeen($senderId);
        return response()->json($response);
    }

    public function getMatchedUsers()
    {
        $matchedUsers = $this->messageService->getMatchedUsers();
        return response()->json($matchedUsers);
    }

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
