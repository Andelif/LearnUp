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
                                        SELECT TutorID FROM tutors WHERE user_id = ? AND status = 'Shortlisted'", [$userId])[0]->count;
        $confirmedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        SELECT TutorID FROM tutors WHERE user_id = ? AND status = 'Confirmed'", [$userId])[0]->count;                                

        $cancelledJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        SELECT TutorID FROM tutors WHERE user_id = ? AND status = 'Cancelled'", [$userId])[0]->count;

        return [
            'appliedJobs' => $appliedJobs,
            'shortlistedJobs' => $shortlistedJobs,
            'confirmedJobs'=>$confirmedJobs,
            'cancelledJobs' => $cancelledJobs
        ];
    }

    private function getLearnerStats($userId)
    {
        $appliedRequests = DB::select("SELECT COUNT(*) as count FROM tuition_requests WHERE LearnerID = ?", [$userId])[0]->count;

        $shortlistedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                         SELECT LearnerID FROM learners WHERE user_id = ? AND status = 'Shortlisted'", [$userId])[0]->count;
         $confirmedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?) AND status = 'Confirmed'", [$userId])[0]->count;                                 

        $cancelledTutors =  DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                         SELECT LearnerID FROM learners WHERE user_id = ? AND status = 'Cancelled'", [$userId])[0]->count;

        return [
            'appliedRequests' => $appliedRequests,
            'shortlistedTutors' => $shortlistedTutors,
            'confirmedTutors' =>$confirmedTutors,
            'cancelledTutors' => $cancelledTutors
        ];
    }

}