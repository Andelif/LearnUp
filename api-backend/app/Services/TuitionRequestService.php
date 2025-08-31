<?php

namespace App\Services;

use App\Models\TuitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class TuitionRequestService
{
    /** Public: list all tuition requests (you can paginate later) */
    public function getAllTuitionRequests()
    {
        return TuitionRequest::orderByDesc('TutionID')->get();
    }

    /** Current learner’s own tuition requests */
    public function getUserTuitionRequests()
    {
        $user = Auth::user();
        if (!$user) return collect();

        // Learner row uses LearnerID in your schema
        $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
        if (!$learnerId) return collect();

        return TuitionRequest::where('LearnerID', $learnerId)
            ->orderByDesc('TutionID')
            ->get();
    }

    /** Find by PK */
    public function getTuitionRequestById(int $id): ?TuitionRequest
    {
        return TuitionRequest::find($id);
    }

    /**
     * Create a new tuition request for the current learner.
     * If a learner profile row does not exist yet, we create a minimal one on the fly.
     */
    public function create(array $data): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return ['error' => 'Unauthorized! Please login as learner to continue', 'status' => 403];
        }

        // Try to find LearnerID for this user
        $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');

        // If there is no learner profile yet, create the minimal row now.
        // NOTE: Assumes other columns are nullable. If your table has NOT NULL constraints,
        // add defaults below accordingly.
        if (!$learnerId) {
            $now = Carbon::now();
            try {
                $learnerId = DB::table('learners')->insertGetId([
                    'user_id'    => $user->id,
                    // Add nullable/default columns here if your schema requires:
                    // 'contact_number' => null,
                    // 'address'        => null,
                    // 'availability'   => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ], 'LearnerID'); // important for Postgres when PK name != "id"
            } catch (\Throwable $e) {
                return [
                    'error'  => 'Could not create minimal learner profile: '.$e->getMessage(),
                    'status' => 500,
                ];
            }
        }

        $payload = [
            'LearnerID'    => $learnerId,
            'class'        => $data['class'],
            'subjects'     => $data['subjects'],
            'asked_salary' => $data['asked_salary'],
            'curriculum'   => $data['curriculum'],
            'days'         => $data['days'],
            'location'     => $data['location'],
        ];

        try {
            $row = DB::transaction(fn () => TuitionRequest::create($payload));
        } catch (\Throwable $e) {
            return [
                'error'  => 'Failed to create tuition request: '.$e->getMessage(),
                'status' => 500,
            ];
        }

        return [
            'message' => 'Tuition request created successfully',
            'data'    => $row,
            'status'  => 201,
        ];
    }

    /** Update an existing request (owner or admin) */
    public function update(int $id, array $data): array
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        $row = TuitionRequest::find($id);
        if (!$row) return ['error' => 'Tuition request not found', 'status' => 404];

        if ($user->role !== 'admin') {
            // Ensure the request belongs to the current learner
            $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
            if (!$learnerId || (string)$row->LearnerID !== (string)$learnerId) {
                return ['error' => 'Forbidden', 'status' => 403];
            }
        }

        $row->fill([
            'class'        => $data['class']        ?? $row->class,
            'subjects'     => $data['subjects']     ?? $row->subjects,
            'asked_salary' => $data['asked_salary'] ?? $row->asked_salary,
            'curriculum'   => $data['curriculum']   ?? $row->curriculum,
            'days'         => $data['days']         ?? $row->days,
            'location'     => $data['location']     ?? $row->location,
        ])->save();

        return ['message' => 'Tuition request updated successfully', 'data' => $row, 'status' => 200];
    }

    /** Delete an existing request (owner or admin) */
    public function delete(int $id): array
    {
        $user = Auth::user();
        if (!$user) return ['error' => 'Unauthorized', 'status' => 401];

        $row = TuitionRequest::find($id);
        if (!$row) return ['error' => 'Tuition request not found', 'status' => 404];

        if ($user->role !== 'admin') {
            $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
            if (!$learnerId || (string)$row->LearnerID !== (string)$learnerId) {
                return ['error' => 'Forbidden', 'status' => 403];
            }
        }

        $row->delete();
        return ['message' => 'Tuition request deleted successfully', 'status' => 200];
    }

    /** Filter (public) */
    public function filter(array $filters)
    {
        $q = DB::table('tuition_requests');

        if (!empty($filters['class'])) {
            $q->where('class', $filters['class']);
        }
        if (!empty($filters['subjects'])) {
            $q->where('subjects', 'like', '%' . $filters['subjects'] . '%');
        }
        if (!empty($filters['location'])) {
            $q->where('location', 'like', '%' . $filters['location'] . '%');
        }

        return $q->orderByDesc('TutionID')->get();
    }
}
