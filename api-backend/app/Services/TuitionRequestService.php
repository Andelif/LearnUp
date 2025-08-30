<?php

namespace App\Services;

use App\Models\TuitionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /** Create a new tuition request for the current learner */
    public function create(array $data): array
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return ['error' => 'Unauthorized! Please login as learner to continue', 'status' => 403];
        }

        $learnerId = DB::table('learners')->where('user_id', $user->id)->value('LearnerID');
        if (!$learnerId) {
            return ['error' => 'Learner profile not found', 'status' => 404];
        }

        $payload = [
            'LearnerID'   => $learnerId,
            'class'       => $data['class'],
            'subjects'    => $data['subjects'],
            'asked_salary'=> $data['asked_salary'],
            'curriculum'  => $data['curriculum'],
            'days'        => $data['days'],
            'location'    => $data['location'],
        ];

        $row = DB::transaction(fn () => TuitionRequest::create($payload));

        return ['message' => 'Tuition request created successfully', 'data' => $row, 'status' => 201];
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
            if (!$learnerId || $row->LearnerID != $learnerId) {
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
            if (!$learnerId || $row->LearnerID != $learnerId) {
                return ['error' => 'Forbidden', 'status' => 403];
            }
        }

        $row->delete();
        return ['message' => 'Tuition request deleted successfully', 'status' => 200];
    }

    /** Filter (public) */
    public function filter(array $filters)
    {
        $q = DB::table('tuition_requests'); // query builder is fine for simple filtering

        if (!empty($filters['class'])) {
            $q->where('class', $filters['class']);
        }
        if (!empty($filters['subjects'])) {
            $q->where('subjects', 'like', '%' . $filters['subjects'] . '%');
        }
        // Uncomment if you add salary min/max later and columns allow numeric compare:
        // if (!empty($filters['asked_salary_min'])) $q->where('asked_salary', '>=', (float)$filters['asked_salary_min']);
        // if (!empty($filters['asked_salary_max'])) $q->where('asked_salary', '<=', (float)$filters['asked_salary_max']);
        if (!empty($filters['location'])) {
            $q->where('location', 'like', '%' . $filters['location'] . '%');
        }

        return $q->orderByDesc(DB::raw('COALESCE("TutionID", 0)')) // safe on your schema
                 ->get();
    }
}
