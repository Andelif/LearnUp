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
        // Return the exact literals your DB CHECK expects
        return match ($g) {
            'male', 'm'   => 'Male',
            'female','f'  => 'Female',
            'other','o'   => 'Other',
            default       => null,
        };
    }

    /** Coerce a mixed truthy/falsey input to a real boolean (or null). */
    private function toBoolOrNull(mixed $v): ?bool
    {
        // Accepts true/false, 1/0, "true"/"false", "1"/"0"
        return filter_var($v, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
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
                // Use NULL instead of empty strings for optional text fields
                $address = isset($data['address']) && $data['address'] !== '' ? $data['address'] : null;

                DB::insert(
                    "INSERT INTO learners (user_id, full_name, contact_number, gender, address)
                     VALUES (?, ?, ?, ?, ?)",
                    [$user->id, $data['name'], $phone, $gender, $address]
                );

            } elseif ($data['role'] === 'tutor') {
                // Coerce/clean optional fields — never pass '' to typed columns
                $qualification   = isset($data['qualification']) && $data['qualification'] !== '' ? $data['qualification'] : null;

                // If your columns allow NULL, prefer null; if NOT NULL, use sensible defaults.
                $experience      = array_key_exists('experience', $data) && $data['experience'] !== ''
                    ? (int)$data['experience'] : 0;

                $preferredSalary = array_key_exists('preferred_salary', $data) && $data['preferred_salary'] !== ''
                    ? (int)$data['preferred_salary'] : 0;

                // availability is BOOLEAN in Postgres → convert; fall back to false if absent/invalid
                $availabilityParsed = $this->toBoolOrNull($data['availability'] ?? null);
                $availability       = ($availabilityParsed === null) ? false : $availabilityParsed;

                $address          = isset($data['address']) && $data['address'] !== '' ? $data['address'] : null;

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
                        $availability, // <- boolean true/false, never ''
                        $address,
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
