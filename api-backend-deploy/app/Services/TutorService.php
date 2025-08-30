<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TutorService{
    public function getAllTutors()
    {
        return DB::select("SELECT * FROM tutors");
    }
    public function updateOrCreateTutorProfile($request)
    {
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
    }
    public function getTutorById($id)
    {
        return DB::select("SELECT * FROM tutors WHERE user_id = ?", [$id]);
    }
    public function updateTutorProfile($request, $id)
    {
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
    }
    public function deleteTutor($id)
    {
        DB::delete("DELETE FROM tutors WHERE user_id = ?", [$id]);
    }
}