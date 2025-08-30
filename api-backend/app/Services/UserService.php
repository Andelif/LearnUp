<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserService
{
    private function normalizeGender(?string $gender): ?string
    {
        if ($gender === null) return null;
        $g = strtolower(trim($gender));
        return match ($g) {
            'male','m'   => 'Male',
            'female','f' => 'Female',
            'other','o'  => 'Other',
            default      => null,
        };
    }

    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            /** @var \App\Models\User $user */
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make($data['password']),
                'role'     => $data['role'],
            ]);

            $gender = $this->normalizeGender($data['gender'] ?? null);
            $phone  = $data['contact_number'] ?? null;

            if ($data['role'] === 'learner') {
                $address = $data['address'] ?? '';
                DB::insert(
                    "INSERT INTO learners (user_id, full_name, contact_number, gender, address)
                     VALUES (?, ?, ?, ?, ?)",
                    [$user->id, $data['name'], $phone, $gender, $address]
                );
            } elseif ($data['role'] === 'tutor') {
                // Provide safe defaults to satisfy NOT NULL / CHECK constraints
                $qualification    = $data['qualification']    ?? '';
                $experience       = isset($data['experience']) ? (int)$data['experience'] : 0;
                $preferredSalary  = isset($data['preferred_salary']) ? (int)$data['preferred_salary'] : 0;
                $availability     = $data['availability']     ?? '';
                $address          = $data['address']          ?? '';

                DB::insert(
                    "INSERT INTO tutors (user_id, full_name, contact_number, gender, qualification, experience, preferred_salary, availability, address)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                    [
                        $user->id,
                        $data['name'],
                        $phone,
                        $gender,
                        $qualification,
                        $experience,
                        $preferredSalary,
                        $availability,
                        $address
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
