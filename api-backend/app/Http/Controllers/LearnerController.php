<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LearnerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized: You are not a learner'], 403);
        }
        
        $learners = DB::select("SELECT * FROM learners");
        return response()->json($learners);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'learner') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        DB::insert("REPLACE INTO learners (user_id, full_name, guardian_full_name, contact_number, gender, address) VALUES (?, ?, ?, ?, ?, ?)", [
            $user->id,
            $request->full_name,
            $request->guardian_full_name,
            $request->contact_number,
            $request->gender,
            $request->address
        ]);

        return response()->json(['message' => 'Learner profile updated successfully'], 201);
    }

    public function show($id)
    {
        $user = Auth::user();
        $learner = DB::select("SELECT * FROM learners WHERE id = ?", [$id]);
        
        if (!$learner || $user->id !== $learner[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return response()->json($learner[0]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $learner = DB::select("SELECT * FROM learners WHERE id = ?", [$id]);
        
        if (!$learner || $user->id !== $learner[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        DB::update("UPDATE learners SET full_name = ?, guardian_full_name = ?, contact_number = ?, gender = ?, address = ? WHERE id = ?", [
            $request->full_name,
            $request->guardian_full_name,
            $request->contact_number,
            $request->gender,
            $request->address,
            $id
        ]);

        return response()->json(['message' => 'Learner profile updated successfully']);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $learner = DB::select("SELECT * FROM learners WHERE id = ?", [$id]);
        
        if (!$learner || $user->id !== $learner[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        DB::delete("DELETE FROM learners WHERE id = ?", [$id]);
        return response()->json(['message' => 'Learner profile deleted successfully']);
    }
}