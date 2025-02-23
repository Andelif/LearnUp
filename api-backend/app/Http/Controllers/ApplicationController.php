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

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized: Only tutors can apply for tuition requests'], 403);
        }

        $request->validate([
            'tution_id' => 'required|exists:tuition_requests,TutionID',
        ]);
        $learner = DB::select("SELECT learner_id FROM tuition_requests WHERE TutionID = ?", [$request->tution_id]);
        if (empty($learner)) {
            return response()->json(['message' => 'Learner not found for this tuition request'], 404);
        }
    
        $learner_id = $learner[0]->learner_id;
        $tutor=DB::select("Select TutorID from tutors where user_id= ?",[$user->id]);

        $tutor_id=$tutor[0]->TutorID;
    

        DB::insert("INSERT INTO applications (tution_id,learner_id ,tutor_id) VALUES (?, ?,?)", [
            
            $request->tution_id,
            $learner_id,
            $tutor_id
        ]);

        return response()->json(['message' => 'Application submitted successfully'], 201);
    }
}

