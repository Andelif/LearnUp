<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DashboardService{
    public function getDashboardStats($userId, $role)
    {
        if ($role === 'tutor') {
            return $this->getTutorStats($userId);
        } elseif ($role === 'learner') {
            return $this->getLearnerStats($userId);
        }

        return ['error' => 'Invalid role'];
    }
    private function getTutorStats($userId)
    {
        $appliedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                    (SELECT TutorID FROM tutors WHERE user_id = ?)", [$userId])[0]->count;

        $shortlistedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        (SELECT TutorID FROM tutors WHERE user_id = ?) AND matched = 1", [$userId])[0]->count;

        return [
            'appliedJobs' => $appliedJobs,
            'shortlistedJobs' => $shortlistedJobs,
        ];
    }

    private function getLearnerStats($userId)
    {
        $appliedRequests = DB::select("SELECT COUNT(*) as count FROM tuition_requests WHERE LearnerID = ?", [$userId])[0]->count;

        $shortlistedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE TutorID IN 
                                         (SELECT TutorID FROM applications WHERE matched = 1)", [])[0]->count;

        return [
            'appliedRequests' => $appliedRequests,
            'shortlistedTutors' => $shortlistedTutors,
        ];
    }

}