<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    // Fetch user by ID
    public static function findById($id)
    {
        return DB::select("SELECT * FROM users WHERE id = ?", [$id])[0] ?? null;
    }

    // Fetch user by email
    public static function findByEmail($email)
    {
        return DB::select("SELECT * FROM users WHERE email = ?", [$email])[0] ?? null;
    }

    // Create a new user
    public static function createUser($data)
    {
        DB::insert("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
            $data['name'],
            $data['email'],
            bcrypt($data['password']),
            $data['role']
        ]);
        return DB::getPdo()->lastInsertId();
    }

    // Define relationships using raw queries
    public static function getLearner($id)
    {
        return DB::select("SELECT * FROM learners WHERE user_id = ?", [$id])[0] ?? null;
    }

    public static function getTutor($id)
    {
        return DB::select("SELECT * FROM tutors WHERE user_id = ?", [$id])[0] ?? null;
    }

    public static function getTuitionRequests($id)
    {
        return DB::select("SELECT * FROM tuition_requests WHERE learner_id = ?", [$id]);
    }

    public static function getApplications($id)
    {
        return DB::select("SELECT * FROM applications WHERE tutor_id = ?", [$id]);
    }

    // Role-based helper methods
    public static function isLearner($id)
    {
        $user = self::findById($id);
        return $user && $user->role === 'learner';
    }

    public static function isTutor($id)
    {
        $user = self::findById($id);
        return $user && $user->role === 'tutor';
    }

    public static function isAdmin($id)
    {
        $user = self::findById($id);
        return $user && $user->role === 'admin';
    }
}