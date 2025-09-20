<?php

namespace App\Services;

use App\Models\Learner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

    public function getAllLearners()
    {
        return Learner::orderByDesc('LearnerID')->get();
    }

    public function getLearnerByUserId(int $userId): ?Learner
    {
        return Learner::where('user_id', $userId)->first();
    }

    public function upsertForUser(int $userId, array $data): Learner
    {
        $payload = $this->onlyAllowed($data);
        $payload['user_id'] = $userId;

        if (array_key_exists('gender', $payload)) {
            $payload['gender'] = $this->normalizeGender($payload['gender']);
        }

        Log::debug("LearnerService upsert payload", ['user_id' => $userId, 'payload' => $payload]);

        $row = DB::transaction(fn () =>
            Learner::updateOrCreate(['user_id' => $userId], $payload)
        );

        Log::debug("LearnerService saved row", ['row' => $row]);
        return $row->fresh();
    }

    public function updateForUser(int $userId, array $data): Learner
    {
        return $this->upsertForUser($userId, $data);
    }

    public function deleteByUserId(int $userId): int
    {
        return Learner::where('user_id', $userId)->delete();
    }

    private function onlyAllowed(array $data): array
    {
        $allowed = [
            'full_name',
            'guardian_full_name',
            'contact_number',
            'guardian_contact_number',
            'gender',
            'address',
            'profile_picture',
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
