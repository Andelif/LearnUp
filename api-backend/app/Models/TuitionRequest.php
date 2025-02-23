<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TuitionRequest extends Model
{
    protected $table = 'tuition_requests';
    protected $primaryKey = 'TuitionID';
    public $incrementing = true;
    protected $fillable = ['LearnerID', 'class', 'subjects', 'asked_salary', 'curriculum', 'days', 'location'];

    // Create a new tuition request
    public static function createTuitionRequest($data)
    {
        DB::insert("INSERT INTO tuition_requests (LearnerID, class, subjects, asked_salary, curriculum, days, location) VALUES (?, ?, ?, ?, ?, ?, ?)", [
            $data['LearnerID'],
            $data['class'],
            $data['subjects'],
            $data['asked_salary'],
            $data['curriculum'],
            $data['days'],
            $data['location']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    // Fetch tuition request by ID
    public static function findById($tuition_id)
    {
        return DB::select("SELECT * FROM tuition_requests WHERE TuitionID = ?", [$tuition_id])[0] ?? null;
    }

    // Get all applications for a tuition request
    public static function getApplications($tuition_id)
    {
        return DB::select("SELECT * FROM applications WHERE tuition_id = ?", [$tuition_id]);
    }
}
