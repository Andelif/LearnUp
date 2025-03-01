<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class TuitionRequest extends Model
{
    protected $table = 'tuition_requests';
    protected $primaryKey = 'TutionID';
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
        return DB::select("SELECT * FROM tuition_requests WHERE TutionID = ?", [$tuition_id])[0] ?? null;
    }

    // Get all applications for a tuition request
    public static function getApplications($tuition_id)
    {
        return DB::select("SELECT * FROM applications WHERE tuition_id = ?", [$tuition_id]);
    }
    public static function filterTuitionRequests($filters)
    {
    $query = "SELECT * FROM tuition_requests WHERE 1=1";
    $params = [];

    if (!empty($filters['class'])) {
        $query .= " AND class = :class";
        $params[':class'] = $filters['class'];
    }

    if (!empty($filters['subjects'])) {
        $query .= " AND subjects LIKE :subjects";
        $params[':subjects'] = '%' . $filters['subjects'] . '%';
    }

    if (!empty($filters['asked_salary_min'])) {
        $query .= " AND asked_salary >= :asked_salary_min";
        $params[':asked_salary_min'] = $filters['asked_salary_min'];
    }

    if (!empty($filters['asked_salary_max'])) {
        $query .= " AND asked_salary <= :asked_salary_max";
        $params[':asked_salary_max'] = $filters['asked_salary_max'];
    }

    if (!empty($filters['location'])) {
        $query .= " AND location LIKE :location";
        $params[':location'] = '%' . $filters['location'] . '%';
    }

    // Directly execute raw SQL query
    return DB::select($query, $params);
   }

}
