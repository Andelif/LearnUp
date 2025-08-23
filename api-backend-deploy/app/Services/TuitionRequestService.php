<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TuitionRequestService{
    public function getAllTuitionRequests()
    {
        return DB::select("SELECT * FROM tuition_requests");
    }


    public function getUserTuitionRequests()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return DB::select("SELECT * FROM tuition_requests WHERE LearnerID = (SELECT LearnerID FROM learners WHERE user_id = ?)", [$user->id]);
    }


    public function getTuitionRequestById($id)
    {
        $tuitionRequest = DB::select("SELECT * FROM tuition_requests WHERE TutionID = ?", [$id]);

        return empty($tuitionRequest) ? null : $tuitionRequest[0];
    }


    public function createTuitionRequest($request)
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
            'location' => 'required|string',
        ]);

        $learner = DB::select("SELECT LearnerID FROM learners WHERE user_id = ?", [$user->id]);

        if (empty($learner)) {
            return response()->json(['message' => 'Learner not found'], 404);
        }

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

        return ['message' => 'Tuition request created successfully'];
    }


    public function updateTuitionRequest($request, $id)
    {
        $tuitionRequest = DB::select("SELECT * FROM tuition_requests WHERE TutionID = ?", [$id]);

        if (empty($tuitionRequest)) {
            return null;
        }

        $request->validate([
            'class' => 'required|string|max:255',
            'subjects' => 'required|string',
            'asked_salary' => 'required|numeric',
            'curriculum' => 'required|string',
            'days' => 'required|string',
            'location' => 'required|string',
        ]);

        DB::update("UPDATE tuition_requests SET class = ?, subjects = ?, asked_salary = ?, curriculum = ?, days = ?, location = ? WHERE TutionID = ?", [
            $request->class,
            $request->subjects,
            $request->asked_salary,
            $request->curriculum,
            $request->days,
            $request->location,
            $id
        ]);

        return ['message' => 'Tuition request updated successfully'];
    }

    
    public function deleteTuitionRequest($id)
    {
        $tuitionRequest = DB::select("SELECT * FROM tuition_requests WHERE TutionID = ?", [$id]);

        if (empty($tuitionRequest)) {
            return null;
        }

        DB::delete("DELETE FROM tuition_requests WHERE TutionID = ?", [$id]);

        return ['message' => 'Tuition Request deleted successfully'];
    }
}