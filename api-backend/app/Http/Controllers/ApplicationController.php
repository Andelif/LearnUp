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
            'tuition_id' => 'required|exists:tuition_requests,id',
        ]);

        DB::insert("INSERT INTO applications (tuition_id, tutor_id) VALUES (?, ?)", [
            $request->tuition_id,
            $user->id
        ]);

        return response()->json(['message' => 'Application submitted successfully'], 201);
    }
}

