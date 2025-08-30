<?php

namespace App\Services;

use App\Models\Tutor;
use Illuminate\Support\Facades\DB;

class TutorService
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

    /** Admin can view all; controller restricts who calls this. */
    public function getAllTutors()
    {
        return Tutor::orderBy('id', 'desc')->get();
    }

    public function getTutorByUserId(int $userId): ?Tutor
    {
        return Tutor::where('user_id', $userId)->first();
    }

    /** Create or update a tutor profile for a given user_id (Postgres-safe). */
    public function upsertForUser(int $userId, array $data): Tutor
    {
        // Provide safe defaults to satisfy NOT NULL / CHECK constraints
        $payload = [
            'full_name'              => $data['full_name']              ?? '',
            'address'                => $data['address']                ?? '',
            'contact_number'         => $data['contact_number']         ?? null,
            'gender'                 => $this->normalizeGender($data['gender'] ?? null),
            'preferred_salary'       => isset($data['preferred_salary']) ? (int)$data['preferred_salary'] : 0,
            'qualification'          => $data['qualification']          ?? '',
            'experience'             => isset($data['experience']) ? (int)$data['experience'] : 0,
            'currently_studying_in'  => $data['currently_studying_in']  ?? '',
            'preferred_location'     => $data['preferred_location']     ?? '',
            'preferred_time'         => $data['preferred_time']         ?? '',
            'availability'           => $data['availability']           ?? '',
        ];

        return DB::transaction(function () use ($userId, $payload) {
            return Tutor::updateOrCreate(
                ['user_id' => $userId],
                $payload
            );
        });
    }

    public function deleteByUserId(int $userId): int
    {
        return Tutor::where('user_id', $userId)->delete();
    }
}
