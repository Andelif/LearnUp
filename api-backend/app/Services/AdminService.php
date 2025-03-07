<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminService{
    public function getAllAdmins()
    {
        return DB::select("SELECT * FROM admins");
    }

    public function createAdmin($data, $userId)
    {
        $exists = DB::select("SELECT * FROM admins WHERE user_id = ?", [$userId]);
        if ($exists) {
            return ['error' => 'User is already an admin'];
        }

        DB::insert("INSERT INTO admins (user_id, full_name, address, contact_number, permission_req) VALUES (?, ?, ?, ?, ?)", [
            $userId,
            $data['full_name'],
            $data['address'],
            $data['contact_number'],
            $data['permission_req']
        ]);

        return ['message' => 'Admin profile updated successfully'];
    }

    public function getAdminById($id)
    {
        return DB::select("SELECT * FROM admins WHERE AdminID = ?", [$id]);
    }

    public function updateAdmin($id, $data)
    {
        DB::update("UPDATE admins SET full_name = ?, address = ?, contact_number = ?, permission_req = ? WHERE AdminID = ?", [
            $data['full_name'],
            $data['address'],
            $data['contact_number'],
            $data['permission_req'],
            $id
        ]);

        return ['message' => 'Admin updated successfully'];
    }

    public function deleteAdmin($id)
    {
        DB::delete("DELETE FROM admins WHERE AdminID = ?", [$id]);
        return ['message' => 'Admin deleted successfully'];
    }

    public function getLearners()
    {
        return DB::select("SELECT * FROM learners");
    }
    public function deleteLearner($learnerId)
    {
        DB::beginTransaction();

        try {
            
            $learner = DB::select('SELECT * FROM learners WHERE LearnerID = ?', [$learnerId]);

            if (empty($learner)) {
               
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => 'Learner not found'
                ];
            }

            
            DB::statement('DELETE FROM learners WHERE LearnerID = ?', [$learnerId]);

            
            DB::statement('DELETE FROM users WHERE id = (SELECT user_id FROM learners WHERE LearnerID = ? LIMIT 1)', [$learnerId]);

           
            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Learner and associated user deleted successfully'
            ];
        } catch (Exception $e) {
           
            DB::rollBack();

            
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the learner: ' . $e->getMessage()
            ];
        }
    }

    public function getTutors()
    {
        return DB::select("SELECT * FROM tutors");
    }
    public function deleteTutor($tutorId)
    {
        DB::beginTransaction();

        try {
            
            $tutor = DB::select('SELECT * FROM tutors WHERE TutorID = ?', [$tutorId]);

            if (empty($tutor)) {
               
                DB::rollBack();
                return [
                    'status' => 'error',
                    'message' => 'Tutor not found'
                ];
            }

            
            DB::statement('DELETE FROM tutors WHERE TutorID = ?', [$tutorId]);

            
            DB::statement('DELETE FROM users WHERE id = (SELECT user_id FROM tutors WHERE TutorID = ? LIMIT 1)', [$tutorId]);

           
            DB::commit();

            return [
                'status' => 'success',
                'message' => 'Tutor and associated user deleted successfully'
            ];
        } catch (Exception $e) {
           
            DB::rollBack();

            
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the tutor: ' . $e->getMessage()
            ];
        }
    }


    public function getTuitionRequests()
    {
        return DB::select("SELECT * FROM tuition_requests");
    }

    public function getApplications()
    {
        return DB::select("SELECT * FROM applications");
    }

    public function getApplicationsByTuitionID($tutionID)
    {
        return DB::select("
            SELECT a.ApplicationID, 
                   t.full_name AS tutor_name, 
                   t.experience, 
                   t.qualification,
                   t.currently_studying_in,
                   t.preferred_salary,
                   t.preferred_location,
                   t.preferred_time
            FROM applications a
            JOIN tutors t ON a.tutor_id = t.TutorID
            WHERE a.tution_id = ?", [$tutionID]);
    }

    public function matchTutor($applicationId)
    {
        $application = DB::select("SELECT * FROM applications WHERE ApplicationID = ?", [$applicationId]);

        if (empty($application)) {
            return ['error' => 'Application not found'];
        }

        DB::update("UPDATE applications SET matched = 1, status = 'Shortlisted' WHERE ApplicationID = ?", [$applicationId]);

        $adminId = Auth::id();
        $application = $application[0];

        $learnerUser = DB::select("SELECT user_id FROM learners WHERE LearnerID = 
                                    (SELECT learner_id FROM applications WHERE ApplicationID = ?)", [$applicationId]);

        $tutorUser = DB::select("SELECT user_id FROM tutors WHERE TutorID = 
                                  (SELECT tutor_id FROM applications WHERE ApplicationID = ?)", [$applicationId]);

        if (!empty($learnerUser)) {
            $learnerUserID = $learnerUser[0]->user_id;
            DB::insert("INSERT INTO notifications (user_id, Message, Type, Status, TimeSent, view) VALUES (?, ?, ?, 'Unread', NOW(), ?)", [
                $adminId,
                "A Tutor has been selected for your Tuition ID: {$application->tution_id}.",
                'Application Update',
                $learnerUserID
            ]);
        }

        if (!empty($tutorUser)) {
            $tutorUserID = $tutorUser[0]->user_id;
            DB::insert("INSERT INTO notifications (user_id, Message, Type, Status, TimeSent, view) VALUES (?, ?, ?, 'Unread', NOW(), ?)", [
                $adminId,
                "You have been selected for Tuition ID: {$application->tution_id}.",
                'Application Update',
                $tutorUserID
            ]);
        }

        return ['message' => 'Tutor successfully matched with learner'];
    }
}