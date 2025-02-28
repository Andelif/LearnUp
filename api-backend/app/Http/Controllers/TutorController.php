<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TutorController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tutors = DB::select("SELECT * FROM tutors");
        return response()->json($tutors);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'tutor') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        DB::insert("REPLACE INTO tutors (user_id, full_name, address, contact_number, gender, preferred_salary, qualification, experience, currently_studying_in) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            Auth::id(),
            $request->full_name,
            $request->address,
            $request->contact_number,
            $request->gender,
            $request->preferred_salary,
            $request->qualification,
            $request->experience,
            $request->currently_studying_in
        ]);

        return response()->json(['message' => 'Tutor profile updated successfully'], 201);
    }

    public function show($id)
    {
        $tutor = DB::select("SELECT * FROM tutors WHERE user_id = ?", [$id]);

        if (!$tutor || (Auth::id() !== $tutor[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($tutor[0]);
    }
   

    public function update(Request $request, $id)
    {
        $tutor = DB::select("SELECT * FROM tutors WHERE user_id = ?", [$id]);

        if (!$tutor || Auth::id() !== $tutor[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::update("UPDATE tutors SET full_name = ?, address = ?, contact_number = ?, gender = ?, preferred_salary = ?, qualification = ?, experience = ?, currently_studying_in = ?, preferred_location = ?, preferred_time = ? WHERE user_id = ?", [
            $request->full_name,
            $request->address,
            $request->contact_number,
            $request->gender,
            $request->preferred_salary,
            $request->qualification,
            $request->experience,
            $request->currently_studying_in,
            $request->preferred_location,
            $request->preferred_time,
            $id
        ]);

        return response()->json(['message' => 'Tutor profile updated successfully']);
    }

    public function destroy($id)
    {
        $tutor = DB::select("SELECT * FROM tutors WHERE user_id = ?", [$id]);

        if (!$tutor || (Auth::id() !== $tutor[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::delete("DELETE FROM tutors WHERE id = ?", [$id]);
        return response()->json(['message' => 'Tutor profile deleted successfully']);
    }
}