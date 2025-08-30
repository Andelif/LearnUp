<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            
            if ($data['role'] === 'learner') {
                DB::insert(
                    "INSERT INTO learners (user_id, full_name, contact_number, gender, address)
                     VALUES (?, ?, ?, ?, ?)",
                    [
                        $user->id,
                        $data['name'],
                        $data['contact_number'] ?? null,
                        $data['gender'] ?? null,
                        null
                    ]
                );
            } elseif ($data['role'] === 'tutor') {
                DB::insert(
                    "INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary, availability, address)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $user->id,
                        $data['name'],
                        $data['contact_number'] ?? null,
                        $data['gender'] ?? null,
                        null, null, null, null, null
                    ]
                );
            } elseif ($data['role'] === 'admin') {
                DB::insert(
                    "INSERT INTO admins (user_id, full_name) VALUES (?, ?)",
                    [$user->id, $data['name']]
                );
            }

            return $user;
        });
    }

    public function updateProfile(int $userId, string $name): void
    {
        DB::update("UPDATE users SET name = ? WHERE id = ?", [$name, $userId]);
    }
}
