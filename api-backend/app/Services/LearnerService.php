<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LearnerService{
    public function getAllLearners()
    {
        return DB::select("SELECT * FROM learners");
    }

    public function createOrUpdateLearner($request)
    {
        return DB::insert("REPLACE INTO learners (user_id, full_name, guardian_full_name, contact_number, gender, address) VALUES (?, ?, ?, ?, ?, ?)", [
            Auth::id(),
            $request->full_name,
            $request->guardian_full_name,
            $request->contact_number,
            $request->gender,
            $request->address
        ]);
    }
    public function getLearnerById($id)
    {
        $learner = DB::select("SELECT * FROM learners WHERE user_id = ?", [$id]);
        return $learner ? $learner[0] : null;
    }

    public function updateLearner($request, $id)
    {
        return DB::update("UPDATE learners SET full_name = ?, guardian_full_name = ?, contact_number = ?, gender = ?, address = ? WHERE user_id = ?", [
            $request->full_name,
            $request->guardian_full_name,
            $request->contact_number,
            $request->gender,
            $request->address,
            $id
        ]);
    }
    public function deleteLearner($id)
    {
        return DB::delete("DELETE FROM learners WHERE user_id = ?", [$id]);
    }
}