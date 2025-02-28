<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Learner extends Model
{
    protected $primaryKey = 'LearnerID';
    public $incrementing = true;

    // Fetch learner by user_id
    public static function findByUserId($user_id)
    {
        return DB::select("SELECT * FROM learners WHERE user_id = ?", [$user_id])[0] ?? null;
    }

    // Create a new learner entry
    public static function createLearner($data)
    {
        DB::insert("INSERT INTO learners (user_id, full_name, guardian_full_name, contact_number, guardian_contact_number, gender, address) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $data['user_id'],
            $data['full_name'],
            $data['guardian_full_name'],
            $data['contact_number'],
            $data['guardian_contact_number'],
            $data['gender'],
            $data['address']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    // Get tuition requests for a learner
    public static function getTuitionRequests($learner_id)
    {
        return DB::select("SELECT * FROM tuition_requests WHERE learner_id = ?", [$learner_id]);
    }
   
}
