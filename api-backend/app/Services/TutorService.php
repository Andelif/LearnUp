<?php

namespace App\Services;

use App\Models\Tutor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        return null;
    }

    public function getAllTutors()
    {
        return Tutor::orderByDesc('TutorID')->get();
    }

    public function getTutorByUserId(int $userId): ?Tutor
    {
        return Tutor::where('user_id', $userId)->first();
    }

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
        if (array_key_exists('availability', $payload)) {
            $norm = $this->normalizeAvailability($payload['availability']);
            if ($norm !== null) $payload['availability'] = $norm;
            else unset($payload['availability']);
        }

        Log::debug("TutorService upsert payload", ['user_id' => $userId, 'payload' => $payload]);

        $row = DB::transaction(fn () =>
            Tutor::updateOrCreate(['user_id' => $userId], $payload)
        );

        Log::debug("TutorService saved row", ['row' => $row]);

        return $row->fresh();
    }

    public function deleteByUserId(int $userId): int
    {
        return Tutor::where('user_id', $userId)->delete();
    }

    private function onlyAllowed(array $data): array
    {
        $allowed = [
            'full_name',
            'address',
            'contact_number',
            'gender',
            'preferred_salary',
            'qualification',
            'experience',
            'currently_studying_in',
            'preferred_location',
            'preferred_time',
            'availability',
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
