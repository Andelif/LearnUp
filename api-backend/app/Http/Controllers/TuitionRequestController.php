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

        DB::insert("INSERT INTO tuition_requests (learner_id, class, subjects, asked_salary, curriculum, days, location) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $user->id,
            $request->class,
            $request->subjects,
            $request->asked_salary,
            $request->curriculum,
            $request->days,
            $request->location
        ]);

        return response()->json(['message' => 'Tuition request created successfully'], 201);
    }
}