<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Learner;
use App\Models\Tutor;
use Illuminate\Support\Facades\DB;

class AdminService
{
    /** List all admins (controller restricts to admin role). */
    public function getAllAdmins()
    {
        return Admin::orderBy('id', 'desc')->get();
    }

    /** Create an admin record for a user if not exists. */
    public function createAdmin(array $data, int $userId): array
    {
        if (Admin::where('user_id', $userId)->exists()) {
            return ['error' => 'User is already an admin'];
        }

        $admin = Admin::create([
            'user_id'        => $userId,
            'full_name'      => $data['full_name'] ?? '',
            'address'        => $data['address'] ?? '',
            'contact_number' => $data['contact_number'] ?? null,
            'permission_req' => (bool)($data['permission_req'] ?? false),
            'match_made'     => (int)($data['match_made'] ?? 0),
            'task_assigned'  => (int)($data['task_assigned'] ?? 0),
        ]);

        return ['message' => 'Admin profile updated successfully', 'data' => $admin];
    }

    public function getAdminById(int $id): ?\App\Models\Admin
    {
        return Admin::find($id);
    }

    public function updateAdmin(int $id, array $data): array
    {
        $admin = Admin::find($id);
        if (!$admin) {
            return ['error' => 'Admin not found'];
        }

        $admin->fill([
            'full_name'      => $data['full_name']      ?? $admin->full_name,
            'address'        => $data['address']        ?? $admin->address,
            'contact_number' => $data['contact_number'] ?? $admin->contact_number,
            'permission_req' => isset($data['permission_req']) ? (bool)$data['permission_req'] : $admin->permission_req,
        ])->save();

        return ['message' => 'Admin updated successfully', 'data' => $admin];
    }

    public function deleteAdmin(int $id): array
    {
        $deleted = Admin::where('id', $id)->delete();
        return ['message' => $deleted ? 'Admin deleted successfully' : 'Nothing to delete'];
    }

    public function getLearners()
    {
        // If your learners PK is LearnerID, ordering by id is harmless; we keep it simple here.
        return Learner::orderBy('id', 'desc')->get();
    }

    /** Delete learner row and associated user (transactional). */
    public function deleteLearner(int $learnerId): array
    {
        return DB::transaction(function () use ($learnerId) {
            $learner = Learner::find($learnerId);
            if (!$learner) {
                return ['status' => 'error', 'message' => 'Learner not found'];
            }

            $userId = $learner->user_id;

            $learner->delete();
            DB::table('users')->where('id', $userId)->delete();

            return ['status' => 'success', 'message' => 'Learner and associated user deleted successfully'];
        });
    }

    public function getTutors()
    {
        return Tutor::orderBy('id', 'desc')->get();
    }

    /** Delete tutor row and associated user (transactional). */
    public function deleteTutor(int $tutorId): array
    {
        return DB::transaction(function () use ($tutorId) {
            $tutor = Tutor::find($tutorId);
            if (!$tutor) {
                return ['status' => 'error', 'message' => 'Tutor not found'];
            }

            $userId = $tutor->user_id;

            $tutor->delete();
            DB::table('users')->where('id', $userId)->delete();

            return ['status' => 'success', 'message' => 'Tutor and associated user deleted successfully'];
        });
    }

    public function getTuitionRequests()
    {
        // Order by the correct PK used in your schema
        return DB::table('tuition_requests')->orderBy('TutionID', 'desc')->get();
    }

    public function getApplications()
    {
        // Order by the correct PK used in your schema
        return DB::table('applications')->orderBy('ApplicationID', 'desc')->get();
    }

    /**
     * Applications for a given tuition request (joins tutor profile columns).
     * Uses a.tution_id (note spelling) and a.ApplicationID, and tutors.TutorID.
     */
    public function getApplicationsByTuitionID(int $tuitionId)
    {
        return DB::table('applications as a')
            ->join('tutors as t', 'a.tutor_id', '=', 't.TutorID') // <-- TutorID PK
            ->where('a.tution_id', $tuitionId)                    // <-- tution_id column
            ->get([
                'a.ApplicationID as application_id',              // <-- ApplicationID PK
                't.full_name as tutor_name',
                't.experience',
                't.qualification',
                't.currently_studying_in',
                't.preferred_salary',
                't.preferred_location',
                't.preferred_time',
            ]);
    }

    /**
     * Mark application as matched & notify both parties.
     * Uses ApplicationID, and resolves learner/tutor via LearnerID/TutorID.
     */
    public function matchTutor(int $applicationId): array
    {
        return DB::transaction(function () use ($applicationId) {
            // Use ApplicationID as per your schema
            $application = DB::table('applications')
                ->where('ApplicationID', $applicationId)
                ->first();

            if (!$application) {
                return ['error' => 'Application not found'];
            }

            DB::table('applications')
                ->where('ApplicationID', $applicationId)
                ->update(['matched' => 1, 'status' => 'Shortlisted']);

            // Resolve user ids of learner and tutor via LearnerID / TutorID
            $learner = DB::table('learners')
                ->where('LearnerID', $application->learner_id)
                ->first();

            $tutor = DB::table('tutors')
                ->where('TutorID', $application->tutor_id)
                ->first();

            $nowExpr = DB::raw('NOW()');
            $tuitionId = $application->tution_id; // note spelling

            if ($learner && $learner->user_id) {
                DB::table('notifications')->insert([
                    'user_id'  => $learner->user_id, // recipient: learner
                    'Message'  => "A Tutor has been selected for your Tuition ID: {$tuitionId}.",
                    'Type'     => 'Application Update',
                    'Status'   => 'Unread',
                    'TimeSent' => $nowExpr,
                    'view'     => 0,
                ]);
            }

            if ($tutor && $tutor->user_id) {
                DB::table('notifications')->insert([
                    'user_id'  => $tutor->user_id, // recipient: tutor
                    'Message'  => "You have been selected for Tuition ID: {$tuitionId}.",
                    'Type'     => 'Application Update',
                    'Status'   => 'Unread',
                    'TimeSent' => $nowExpr,
                    'view'     => 0,
                ]);
            }

            return ['message' => 'Tutor successfully matched with learner'];
        });
    }
}
