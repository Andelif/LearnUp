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

    private function normalizeAvailability(mixed $v): ?bool
    {
        if ($v === null) return null;
        if ($v === true || $v === 1 || $v === '1' || $v === 'true')  return true;
        if ($v === false || $v === 0 || $v === '0' || $v === 'false') return false;
        return null; // unknown text → don’t overwrite
    }

    /** Admin list (controller already guards). Order by real PK. */
    public function getAllTutors()
    {
        return Tutor::orderByDesc('TutorID')->get();
    }

    /** Fetch a single tutor profile by users.id (FK). */
    public function getTutorByUserId(int $userId): ?Tutor
    {
        return Tutor::where('user_id', $userId)->first();
    }

    /**
     * Create or update a tutor profile for a given user_id.
     * POST /api/tutors calls this; controller has validation.
     */
    public function upsertForUser(int $userId, array $data): Tutor
    {
        $payload = $this->onlyAllowed($data);
        $payload['user_id'] = $userId;

        if (array_key_exists('gender', $payload)) {
            $payload['gender'] = $this->normalizeGender($payload['gender']);
        }
        if (array_key_exists('preferred_salary', $payload) && $payload['preferred_salary'] !== null) {
            $payload['preferred_salary'] = (int) $payload['preferred_salary'];
        }
        // experience column is varchar(255) in your DB, so keep it as string if provided
        if (array_key_exists('availability', $payload)) {
            $norm = $this->normalizeAvailability($payload['availability']);
            if ($norm !== null) $payload['availability'] = $norm;
            else unset($payload['availability']); // avoid writing invalid text to bool column
        }

        $row = DB::transaction(fn () =>
            Tutor::updateOrCreate(['user_id' => $userId], $payload)
        );

        return $row->fresh();
    }

    /** Delete by users.id; returns number of deleted rows. */
    public function deleteByUserId(int $userId): int
    {
        return Tutor::where('user_id', $userId)->delete();
    }

    /** Write only real DB columns. */
    private function onlyAllowed(array $data): array
    {
        $allowed = [
            'full_name',
            'address',
            'contact_number',
            'gender',
            'preferred_salary',
            'qualification',
            'experience',              // varchar(255)
            'currently_studying_in',
            'preferred_location',
            'preferred_time',
            'availability',            // bool
        ];

        $out = [];
        foreach ($allowed as $k) {
            if (array_key_exists($k, $data)) {
                $out[$k] = $data[$k];
            }
        }
        return $out;
    }
}
