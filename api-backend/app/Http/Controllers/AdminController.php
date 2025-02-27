<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $admins = DB::select("SELECT * FROM admins");
        return response()->json($admins);
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $exists = DB::select("SELECT * FROM admins WHERE user_id = ?", [Auth::id()]);
         if ($exists) {
           return response()->json(['message' => 'User is already an admin'], 400);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:15',
            'permission_req' => 'nullable|boolean',
        ]);

        DB::insert("INSERT INTO admins (user_id, full_name, address, contact_number, permission_req) VALUES (?, ?, ?, ?, ?)", [
            Auth::id(),
            $request->full_name,
            $request->address,
            $request->contact_number,
            $request->permission_req
        ]);

        return response()->json(['message' => 'Admin profile updated successfully'], 201);
    }

    public function show($id)
    {
        $admin = DB::select("SELECT * FROM admins WHERE AdminID = ?", [$id]);
        
        if (!$admin || (Auth::id() !== $admin[0]->user_id && Auth::user()->role !== 'admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($admin[0]);
    }

    public function update(Request $request, $id)
    {
        $admin = DB::select("SELECT * FROM admins WHERE AdminID = ?", [$id]);
        
        if (!$admin || Auth::id() !== $admin[0]->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::update("UPDATE admins SET full_name = ?, address = ?, contact_number = ?, permission_req = ? WHERE AdminID = ?", [
            $request->full_name,
            $request->address,
            $request->contact_number,
            $request->permission_req,
            $id
        ]);

        return response()->json(['message' => 'Admin updated successfully']);
    }

    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        DB::delete("DELETE FROM admins WHERE AdminID = ?", [$id]);
        return response()->json(['message' => 'Admin deleted successfully']);
    }







    public function getLearners()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $learners = DB::select("SELECT * FROM learners");
        return response()->json($learners);
    }

    public function getTutors()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tutors = DB::select("SELECT * FROM tutors");
        return response()->json($tutors);
    }

    public function getTuitionRequests()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $tuitionRequests = DB::select("SELECT * FROM tuition_requests");
        return response()->json($tuitionRequests);
    }

    public function getApplications()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $applications = DB::select("SELECT * FROM applications");
        return response()->json($applications);
    }


    public function getApplicationsByTuitionID($tutionID)
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $applications = DB::select("
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

        return response()->json($applications);
    }

}
