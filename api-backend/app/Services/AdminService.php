<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class AdminService
{
    /** List all admins (controller restricts to admin role). */
    public function getAllAdmins()
    {
        return DB::table('admins')->orderByDesc('AdminID')->get();
    }

    /** Create an admin record for a user if not exists. */
    public function createAdmin(array $data, int $userId): array
    {
        $exists = DB::table('admins')->where('user_id', $userId)->exists();
        if ($exists) {
            return ['error' => 'User is already an admin'];
        }

        $row = [
            'user_id'        => $userId,
            'full_name'      => $data['full_name']      ?? '',
            'address'        => $data['address']        ?? '',
            'contact_number' => $data['contact_number'] ?? null,
            'permission_req' => (bool)($data['permission_req'] ?? false),
            'match_made'     => (int)($data['match_made']     ?? 0),
            'task_assigned'  => (int)($data['task_assigned']  ?? 0),
        ];

        DB::table('admins')->insert($row);

        $admin = DB::table('admins')->where('user_id', $userId)->first();

        return ['message' => 'Admin profile updated successfully', 'data' => $admin];
    }

    public function getAdminById(int $id): ?object
    {
        return DB::table('admins')->where('AdminID', $id)->first();
    }

    public function updateAdmin(int $id, array $data): array
    {
        $admin = DB::table('admins')->where('AdminID', $id)->first();
        if (!$admin) {
            return ['error' => 'Admin not found'];
        }

        $patch = [];
        if (array_key_exists('full_name', $data))      $patch['full_name']      = $data['full_name'];
        if (array_key_exists('address', $data))        $patch['address']        = $data['address'];
        if (array_key_exists('contact_number', $data)) $patch['contact_number'] = $data['contact_number'];
        if (array_key_exists('permission_req', $data)) $patch['permission_req'] = (bool)$data['permission_req'];

        if ($patch) {
            DB::table('admins')->where('AdminID', $id)->update($patch);
        }

        $fresh = DB::table('admins')->where('AdminID', $id)->first();
        return ['message' => 'Admin updated successfully', 'data' => $fresh];
    }

    public function deleteAdmin(int $id): array
    {
        $deleted = DB::table('admins')->where('AdminID', $id)->delete();
        return ['message' => $deleted ? 'Admin deleted successfully' : 'Nothing to delete'];
    }

    /** Admin: list learners (LearnerID PK). */
    public function getLearners()
    {
        return DB::table('learners')->orderByDesc('LearnerID')->get();
    }

    /** Delete learner row and associated user (transactional). */
    public function deleteLearner(int $learnerId): array
    {
        return DB::transaction(function () use ($learnerId) {
            $learner = DB::table('learners')->where('LearnerID', $learnerId)->first();
            if (!$learner) {
                return ['status' => 'error', 'message' => 'Learner not found'];
            }

            $userId = $learner->user_id;

            DB::table('learners')->where('LearnerID', $learnerId)->delete();
            if ($userId) {
                DB::table('users')->where('id', $userId)->delete();
            }

            return ['status' => 'success', 'message' => 'Learner and associated user deleted successfully'];
        });
    }

    /** Admin: list tutors (TutorID PK). */
    public function getTutors()
    {
        return DB::table('tutors')->orderByDesc('TutorID')->get();
    }

    /** Delete tutor row and associated user (transactional). */
    public function deleteTutor(int $tutorId): array
    {
        return DB::transaction(function () use ($tutorId) {
            $tutor  = DB::table('tutors')->where('TutorID', $tutorId)->first();
            if (!$tutor) {
                return ['status' => 'error', 'message' => 'Tutor not found'];
            }

            $userId = $tutor->user_id;

            DB::table('tutors')->where('TutorID', $tutorId)->delete();
            if ($userId) {
                DB::table('users')->where('id', $userId)->delete();
            }

            return ['status' => 'success', 'message' => 'Tutor and associated user deleted successfully'];
        });
    }

    /** Admin: list requests (TutionID PK). */
    public function getTuitionRequests()
    {
        return DB::table('tuition_requests')->orderByDesc('TutionID')->get();
    }

    /** Admin: list applications (ApplicationID PK). */
    public function getApplications()
    {
        return DB::table('applications')->orderByDesc('ApplicationID')->get();
    }

    /**
     * Applications for a given tuition request (joins tutor profile).
     * NOTE: returns camel-safe keys.
     */
    public function getApplicationsByTuitionID(int $tuitionId)
    {
        return DB::table('applications as a')
            ->join('tutors as t', 'a.tutor_id', '=', 't.TutorID')
            ->where('a.tution_id', $tuitionId)
            ->orderByDesc('a.ApplicationID')
            ->get([
                'a.ApplicationID as application_id',
                'a.matched',
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
     * Mark application as matched + notify both parties.
     * NO insertion into confirmed_tuitions here.
     */
   public function matchTutor(int $applicationId): array
    {
        return DB::transaction(function () use ($applicationId) {
            $application = DB::table('applications')
                ->where('ApplicationID', $applicationId)
                ->first();

            if (!$application) {
                return ['error' => 'Application not found'];
            }

            if ((int)($application->matched ?? 0) === 1) {
                return ['message' => 'Application already matched'];
            }

            $tutor = DB::table('tutors')->where('TutorID', $application->tutor_id)->first();
            $learner = $application->learner_id
                ? DB::table('learners')->where('LearnerID', $application->learner_id)->first()
                : null;

            // Only mark matched/shortlisted (no confirmed_tuitions insert here)
            DB::table('applications')
                ->where('ApplicationID', $applicationId)
                ->update(['matched' => 1, 'status' => 'Shortlisted']);

            $tuitionId = $application->tution_id;
            $nowExpr   = DB::raw('CURRENT_TIMESTAMP');

            if ($learner && !empty($learner->user_id)) {
                DB::table('notifications')->insert([
                    'user_id'  => $learner->user_id,
                    'Message'  => "A tutor has been selected for your Tuition ID: {$tuitionId}.",
                    'Type'     => 'Application Update',
                    'Status'   => 'Unread',
                    'TimeSent' => $nowExpr,
                    'view'     => 0, // <- NOT NULL
                ]);
            }

            if ($tutor && !empty($tutor->user_id)) {
                DB::table('notifications')->insert([
                    'user_id'  => $tutor->user_id,
                    'Message'  => "You have been selected for Tuition ID: {$tuitionId}.",
                    'Type'     => 'Application Update',
                    'Status'   => 'Unread',
                    'TimeSent' => $nowExpr,
                    'view'     => 0, // <- NOT NULL
                ]);
            }

            return ['message' => 'Tutor successfully matched with learner'];
        });
    }

}
