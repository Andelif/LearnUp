<?php

namespace App\Http\Controllers;

use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    protected MessageService $svc;

    public function __construct(MessageService $svc)
    {
        $this->svc = $svc;
    }

    /** POST /api/messages */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'SentTo'  => 'required|integer|exists:users,id',
            'Content' => 'required|string|max:2000',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $r = $this->svc->sendMessage((int)$validator->validated()['SentTo'], $validator->validated()['Content']);

        if (!empty($r['error'])) return response()->json(['message' => $r['error']], $r['status'] ?? 400);
        return response()->json(['message' => $r['message'], 'data' => $r['data']], $r['status'] ?? 201);
    }

    /** GET /api/messages/{userId} */
    public function getMessages($userId)
    {
        $rows = $this->svc->getMessages((int)$userId);
        if (isset($rows['error'])) {
            return response()->json(['message' => $rows['error']], $rows['status']);
        }
        return response()->json($rows, 200);
    }

    /** PUT /api/messages/seen/{senderId} */
    public function markAsSeen($senderId)
    {
        $r = $this->svc->markAsSeen((int)$senderId);
        if (!empty($r['error'])) return response()->json(['message' => $r['error']], $r['status']);
        return response()->json(['message' => $r['message'], 'updated' => $r['updated']], $r['status']);
    }

    /** GET /api/matched-users */
    public function getMatchedUsers()
    {
        $rows = $this->svc->getMatchedUsers();
        if (isset($rows['error'])) {
            return response()->json(['message' => $rows['error']], $rows['status']);
        }
        return response()->json($rows, 200);
    }

    /** POST /api/reject-tutor */
    public function rejectTutor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // IMPORTANT: this must be the Tutor's **TutorID** (PK), not user_id
            'tutor_id'  => 'required|integer|exists:tutors,TutorID',
            // 'tution_id' uses your applications column spelling
            'tution_id' => 'required|integer|exists:applications,tution_id',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $r = $this->svc->rejectTutor((int)$validator->validated()['tutor_id'], (int)$validator->validated()['tution_id']);

        if (!empty($r['error'])) return response()->json(['message' => $r['error']], $r['status']);
        return response()->json(['message' => $r['message']], $r['status']);
    }
}
