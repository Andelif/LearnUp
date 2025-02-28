<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function index()
    {
        $applications = DB::select("SELECT * FROM applications");
        return response()->json($applications);
    }
    public function getTutorStats(Request $request, $userId)
    {
        $tutor=DB::select("SELECT TutorID FROM tutors WHERE user_id = ?", [$userId]);
        if (empty($tutor)) {
            return response()->json(['message' => 'Tutor not found'], 404);
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

        return response()->json($results[0]);
    }
    public function getLearnerStats(Request $request, $userId)
    {
        $learner=DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$userId]);
        if (empty($learner)) {
            return response()->json(['message' => 'Learner not found'], 404);
        }
    
        $learnerId = $learner[0]->LearnerID;
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? ) AS applied,
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'shortlisted') AS shortlisted,
                
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'confirmed') AS confirmed,
                (SELECT COUNT(*) FROM applications WHERE learner_id = ? AND status = 'cancelled') AS cancelled;
        ";

        $results = DB::select($query, [$learnerId ,$learnerId ,$learnerId ,$learnerId ]);

        return response()->json($results[0]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized: Only tutors can apply for tuition requests'], 403);
        }

        $request->validate([
            'tution_id' => 'required|exists:tuition_requests,TutionID',
        ]);
        $learner = DB::select("SELECT LearnerID FROM tuition_requests WHERE TutionID = ?", [$request->tution_id]);
        if (empty($learner)) {
            return response()->json(['message' => 'Learner not found for this tuition request'], 404);
        }
    
        $learner_id = $learner[0]->LearnerID;
        $tutor=DB::select("Select TutorID from tutors where user_id= ?",[$user->id]);

        $tutor_id=$tutor[0]->TutorID;
    

        DB::insert("INSERT INTO applications (tution_id,learner_id ,tutor_id, matched) VALUES (?, ?, ?, ?)", [
            
            $request->tution_id,
            $learner_id,
            $tutor_id, 
            false
        ]);

        return response()->json(['message' => 'Application submitted successfully'], 201);
    }
}

