<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TuitionRequestController extends Controller
{
    public function index()
    {
        $tuitionRequests = DB::select("SELECT * FROM tuition_requests");
        return response()->json($tuitionRequests);
    }
    public function getAllRequests()
   {
    $user = Auth::user(); // Get authenticated user

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }
    $tuitionRequests = DB::select("
        select * from tuition_requests where LearnerID=(select LearnerID from learners where user_id= ?)
    ",[$user->id]);

    return response()->json($tuitionRequests);
    }

    public function show($id)
    {
        $tuitionRequest = DB::select("SELECT * FROM tuition_requests WHERE TutionID = ?", [$id]);

        if (empty($tuitionRequest)) {
            return response()->json(['message' => 'Tuition request not found'], 404);
        }

        return response()->json($tuitionRequest[0]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized! Please login to continue'], 403);
        }
        
        $request->validate([
            'class' => 'required|string|max:255',
            'subjects' => 'required|string',
            'asked_salary' => 'required|numeric',
            'curriculum' => 'required|string',
            'days' => 'required|string',
            'location'=>'required|string',
        ]);
        $learner = DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$user->id]);
        $learner_id = $learner[0]->LearnerID;
        DB::insert("INSERT INTO tuition_requests (LearnerID, class, subjects, asked_salary, curriculum, days, location) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $learner_id,
            $request->class,
            $request->subjects,
            $request->asked_salary,
            $request->curriculum,
            $request->days,
            $request->location
        ]);
        

        return response()->json(['message' => 'Tuition request created successfully'], 201);
    }
    public function filterTuitionRequests(Request $request)
{
    
    $filters = $request->only(['class', 'subjects', 'asked_salary_min', 'asked_salary_max', 'location']);
    
    
    $filteredRequests = TuitionRequest::filterTuitionRequests($filters);

    return response()->json($filteredRequests);
}
}