<?php

namespace App\Services;

use App\Models\Learner;
use Illuminate\Support\Facades\DB;

class LearnerService
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

    /** Admin list (controller already guards). Order by real PK. */
    public function getAllLearners()
    {
        return Learner::orderByDesc('LearnerID')->get();
    }

    /** Fetch a single learner profile by users.id (FK). */
    public function getLearnerByUserId(int $userId): ?Learner
    {
        return Learner::where('user_id', $userId)->first();
    }

    /**
     * Create or update the learner row for a given user.
     * POST /api/learners calls this; controller has validation.
     */
    public function upsertForUser(int $userId, array $data): Learner
    {
        $payload = $this->onlyAllowed($data);
        $payload['user_id'] = $userId;
        if (array_key_exists('gender', $payload)) {
            $payload['gender'] = $this->normalizeGender($payload['gender']);
        }

        $row = DB::transaction(fn () =>
            Learner::updateOrCreate(['user_id' => $userId], $payload)
        );

        return $row->fresh();
    }

    /**
     * PUT /api/learners/{userId}
     * Idempotent: create if missing, update otherwise.
     */
    public function updateForUser(int $userId, array $data): Learner
    {
        return $this->upsertForUser($userId, $data);
    }

    /** Delete by users.id; returns number of deleted rows. */
    public function deleteByUserId(int $userId): int
    {
        return Learner::where('user_id', $userId)->delete();
    }

    /** Write only real DB columns. */
    private function onlyAllowed(array $data): array
    {
        $allowed = [
            'full_name',
            'guardian_full_name',
            'contact_number',
            'guardian_contact_number',
            'gender',
            'address',
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
