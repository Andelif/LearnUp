<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;


class Tutor extends Model
{
    protected $primaryKey = 'TutorID';
    public $incrementing = true;

    // Fetch tutor by user_id
    public static function findByUserId($user_id)
    {
        return DB::select("SELECT * FROM tutors WHERE user_id = ?", [$user_id])[0] ?? null;
    }

    // Create a new tutor entry
    public static function createTutor($data)
    {
        DB::insert("INSERT INTO tutors (user_id, full_name, address, contact_number, gender, preferred_salary, qualification, experience, currently_studying_in, preferred_location, preferred_time, availability) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
            $data['user_id'],
            $data['full_name'],
            $data['address'],
            $data['contact_number'],
            $data['gender'],
            $data['preferred_salary'],
            $data['qualification'],
            $data['experience'],
            $data['currently_studying_in'],
            $data['preferred_location'],
            $data['preferred_time'],
            $data['availability']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    // Get applications submitted by a tutor
    public static function getApplications($tutor_id)
    {
        return DB::select("SELECT * FROM applications WHERE tutor_id = ?", [$tutor_id]);
    }
}

