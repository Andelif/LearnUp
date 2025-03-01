<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationService{
    public function getAllApplications()
    {
        return DB::select("SELECT * FROM applications");
    }

    public function getTutorStats($userId)
    {
        $tutor = DB::select("SELECT TutorID FROM tutors WHERE user_id = ?", [$userId]);
        if (empty($tutor)) {
            return null;
        }

        $tutorId = $tutor[0]->TutorID;
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM applications WHERE tutor_id = ? ) AS applied,
                (SELECT COUNT(*) FROM applications WHERE tutor_id = ? AND status = 'shortlisted') AS shortlisted,
                (SELECT COUNT(*) FROM applications WHERE tutor_id = ? AND status = 'confirmed') AS confirmed,
                (SELECT COUNT(*) FROM applications WHERE tutor_id = ? AND status = 'cancelled') AS cancelled;
        ";

        $results = DB::select($query, [$tutorId, $tutorId, $tutorId, $tutorId]);

        return $results[0];
    }

    public function getLearnerStats($userId)
    {
        $learner = DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$userId]);
        if (empty($learner)) {
            return null;
        }

        $learnerId = $learner[0]->LearnerID;
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? ) AS applied,
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'shortlisted') AS shortlisted,
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'confirmed') AS confirmed,
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'cancelled') AS cancelled;
        ";

        $results = DB::select($query, [$learnerId, $learnerId, $learnerId, $learnerId]);

        return $results[0];
    }

    public function createApplication($request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return ['error' => 'Unauthorized: Only tutors can apply for tuition requests', 'status' => 403];
        }

        $request->validate([
            'tution_id' => 'required|exists:tuition_requests,TutionID',
        ]);

        $learner = DB::select("SELECT LearnerID FROM tuition_requests WHERE TutionID = ?", [$request->tution_id]);
        if (empty($learner)) {
            return ['error' => 'Learner not found for this tuition request', 'status' => 404];
        }

        $learner_id = $learner[0]->LearnerID;
        $tutor = DB::select("SELECT TutorID FROM tutors WHERE user_id = ?", [$user->id]);

        if (empty($tutor)) {
            return ['error' => 'Tutor not found', 'status' => 404];
        }

        $tutor_id = $tutor[0]->TutorID;

        DB::insert("INSERT INTO applications (tution_id, learner_id, tutor_id, matched) VALUES (?, ?, ?, ?)", [
            $request->tution_id,
            $learner_id,
            $tutor_id,
            false
        ]);

        return ['message' => 'Application submitted successfully', 'status' => 201];
    }
}