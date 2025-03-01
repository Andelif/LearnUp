<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserService{
    public function register($data)
    {
        try {
            // Insert user into users table
            DB::insert("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)", [
                $data['name'],
                $data['email'],
                Hash::make($data['password']),
                $data['role']
            ]);

            // Get the newly created user ID
            $userId = DB::getPdo()->lastInsertId();

            // Insert into role-specific tables
            if ($data['role'] === 'learner') {
                DB::insert("INSERT INTO learners (user_id, full_name, contact_number, gender, address) VALUES (?, ?, ?, ?, ?)", [
                    $userId,
                    $data['name'],
                    $data['contact_number'] ?? null,
                    $data['gender'] ?? null,
                    null
                ]);
            } elseif ($data['role'] === 'tutor') {
                DB::insert("INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary, availability, address) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)", [
                    $userId,
                    $data['name'],
                    $data['contact_number'] ?? null,
                    $data['gender'] ?? null,
                    null,
                    null,
                    null,
                    null,
                    null
                ]);
            } elseif ($data['role'] === 'admin') {
                DB::insert("INSERT INTO admins (user_id, full_name) VALUES (?, ?)", [$userId, $data['name']]);
            }

            return $userId;
        } catch (\Exception $e) {
            throw new \Exception("Registration failed: " . $e->getMessage());
        }
    }
    
    public function updateProfile($userId, $name)
    {
        DB::update("UPDATE users SET name = ? WHERE id = ?", [$name, $userId]);
    }
}