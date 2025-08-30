<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MessageService
{
    public function sendMessage(int $sentTo, string $content): array
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        if ($sentTo === (int)$user->id) {
            return ['error' => 'Cannot send a message to yourself', 'status' => 400];
        }

        $exists = DB::table('users')->where('id', $sentTo)->exists();
        if (!$exists) return ['error' => 'Recipient not found', 'status' => 404];

        $row = Message::create([
            'SentBy'   => $user->id,
            'SentTo'   => $sentTo,
            'Content'  => $content,
            'Status'   => 'Delivered',
            'TimeStamp'=> now(),
        ]);

        return ['message' => 'Message sent successfully', 'data' => $row, 'status' => 201];
    }

    public function getMessages(int $otherUserId)
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        // Conversation between auth user and other user
        $rows = DB::table('messages as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.SentBy')
            ->where(function ($q) use ($user, $otherUserId) {
                $q->where('m.SentBy', $user->id)->where('m.SentTo', $otherUserId);
            })
            ->orWhere(function ($q) use ($user, $otherUserId) {
                $q->where('m.SentBy', $otherUserId)->where('m.SentTo', $user->id);
            })
            ->orderBy('m.TimeStamp', 'asc')
            ->get([
                'm.MessageID',
                'm.SentBy',
                'm.SentTo',
                'm.Content',
                'm.Status',
                'm.TimeStamp',
                'u.name as sender_name',
            ]);

        return $rows;
    }

    public function markAsSeen(int $senderId): array
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        $updated = DB::table('messages')
            ->where('SentBy', $senderId)
            ->where('SentTo', $user->id)
            ->where('Status', 'Delivered')
            ->update(['Status' => 'Seen']);

        return ['message' => 'Messages marked as seen', 'updated' => $updated, 'status' => 200];
    }

    /**
     * Return matched counterpart users for the current user (tutor ↔ learner).
     * Uses applications.matched = true and maps through TutorID/LearnerID.
     */
    public function getMatchedUsers()
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        $role = DB::table('users')->where('id', $user->id)->value('role');

        if ($role === 'tutor') {
            $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
            if (!$tutorId) return [];

            return DB::table('learners as l')
                ->join('applications as a', 'l.LearnerID', '=', 'a.learner_id')
                ->where('a.matched', true)
                ->where('a.tutor_id', $tutorId)
                ->get([
                    'l.user_id',
                    'l.full_name',
                    DB::raw("'learner' as role"),
                    'a.tution_id',
                    'a.ApplicationID',
                ]);
        }

        if ($role === 'learner') {
            $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
            if (!$learnerId) return [];

            return DB::table('tutors as t')
                ->join('applications as a', 't.TutorID', '=', 'a.tutor_id')
                ->where('a.matched', true)
                ->where('a.learner_id', $learnerId)
                ->get([
                    't.user_id',
                    't.full_name',
                    DB::raw("'tutor' as role"),
                    'a.tution_id',
                    'a.ApplicationID',
                ]);
        }

        return []; // admins (or other roles) get empty by default
    }

    /**
     * Learner rejects a tutor for a specific tuition (sets status = 'Cancelled').
     * Expects tutor_id to be the **TutorID** (not the tutor user_id).
     */
    public function rejectTutor(int $tutorId, int $tutionId): array
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
        if (!$learnerId) return ['error' => 'Only learners can reject tutors', 'status' => 403];

        $updated = DB::table('applications')
            ->where('tutor_id', $tutorId)     // TutorID
            ->where('learner_id', $learnerId) // LearnerID
            ->where('tution_id', $tutionId)
            ->update(['status' => 'Cancelled', 'matched' => false]);

        if ($updated > 0) {
            return ['message' => 'Tutor rejected successfully', 'status' => 200];
        }
        return ['error' => 'Unable to reject tutor (no matching application found)', 'status' => 404];
    }
}
