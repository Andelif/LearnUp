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

    /** Admin: all learners; otherwise we’ll fetch per-user in controller */
    public function getAllLearners()
    {
        return Learner::orderBy('id', 'desc')->get();
    }

    public function getLearnerByUserId(int $userId): ?Learner
    {
        return Learner::where('user_id', $userId)->first();
    }

    /** Create or update the learner row for a given user_id */
    public function upsertForUser(int $userId, array $data): Learner
    {
        $payload = [
            'full_name'              => $data['full_name']              ?? '',
            'guardian_full_name'     => $data['guardian_full_name']     ?? null,
            'contact_number'         => $data['contact_number']         ?? null,
            'guardian_contact_number'=> $data['guardian_contact_number']?? null,
            'gender'                 => $this->normalizeGender($data['gender'] ?? null),
            'address'                => $data['address']                ?? null,
        ];

        return DB::transaction(function () use ($userId, $payload) {
            return Learner::updateOrCreate(
                ['user_id' => $userId],
                $payload
            );
        });
    }

    public function updateForUser(int $userId, array $data): Learner
    {
        return $this->upsertForUser($userId, $data);
    }

    public function deleteByUserId(int $userId): int
    {
        return Learner::where('user_id', $userId)->delete();
    }
}
