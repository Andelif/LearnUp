<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardStats(int $userId, string $role): array
    {
        return match ($role) {
            'tutor'   => $this->getTutorStats($userId),
            'learner' => $this->getLearnerStats($userId),
            default   => ['error' => 'Invalid role'],
        };
    }

    private function getTutorStats(int $userId): array
    {
        $tutorId = DB::table('tutors')->where('user_id', $userId)->value('TutorID');
        if (!$tutorId) {
            return [
                'appliedJobs'     => 0,
                'shortlistedJobs' => 0,
                'confirmedJobs'   => 0,
                'cancelledJobs'   => 0,
            ];
        }

        return [
            'appliedJobs'     => DB::table('applications')->where('tutor_id', $tutorId)->count(),
            'shortlistedJobs' => DB::table('applications')->where('tutor_id', $tutorId)->where('status', 'Shortlisted')->count(),
            'confirmedJobs'   => DB::table('applications')->where('tutor_id', $tutorId)->where('status', 'Confirmed')->count(),
            'cancelledJobs'   => DB::table('applications')->where('tutor_id', $tutorId)->where('status', 'Cancelled')->count(),
        ];
    }

    private function getLearnerStats(int $userId): array
    {
        $learnerId = DB::table('learners')->where('user_id', $userId)->value('LearnerID');
        if (!$learnerId) {
            return [
                'appliedRequests'  => 0,
                'shortlistedTutors'=> 0,
                'confirmedTutors'  => 0,
                'cancelledTutors'  => 0,
            ];
        }

        return [
            // number of tuition requests created by the learner
            'appliedRequests'   => DB::table('tuition_requests')->where('LearnerID', $learnerId)->count(),
            // application statuses on those requests
            'shortlistedTutors' => DB::table('applications')->where('learner_id', $learnerId)->where('status', 'Shortlisted')->count(),
            'confirmedTutors'   => DB::table('applications')->where('learner_id', $learnerId)->where('status', 'Confirmed')->count(),
            'cancelledTutors'   => DB::table('applications')->where('learner_id', $learnerId)->where('status', 'Cancelled')->count(),
        ];
    }
}
