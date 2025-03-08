<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboardStats($userId, $role)
    {
        if ($role === 'tutor') {



            
            $appliedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        (SELECT TutorID FROM tutors WHERE user_id = ?)", [$userId])[0]->count;

            
            $shortlistedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE status = 'Shortlisted' AND tutor_id = 
                                            (SELECT TutorID FROM tutors WHERE user_id = ?)", [$userId])[0]->count;
            $confirmedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        ( SELECT TutorID FROM tutors WHERE user_id = ?) AND status = 'Confirmed'", [$userId])[0]->count;                                
    

            $cancelledJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                            (SELECT TutorID FROM tutors WHERE user_id = ?) AND status = 'Cancelled'", [$userId])[0]->count;


            return response()->json([
                'appliedJobs' => $appliedJobs,
                'shortlistedJobs' => $shortlistedJobs,
                'confirmedJobs'=> $confirmedJobs,
                'cancelledJobs' => $cancelledJobs,
            ]);



        } elseif ($role === 'learner') {



            
            $appliedRequests = DB::select("SELECT COUNT(*) as count FROM tuition_requests WHERE LearnerID = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?)", [$userId])[0]->count;

            $shortlistedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?) AND status = 'Shortlisted'", [$userId])[0]->count;
            $confirmedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?) AND status = 'Confirmed'", [$userId])[0]->count;                                

            $cancelledTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?) AND status = 'Cancelled'", [$userId])[0]->count;


            return response()->json([
                'appliedRequests' => $appliedRequests,
                'shortlistedTutors' => $shortlistedTutors,
                'confirmedTutors' =>$confirmedTutors,
                'cancelledTutors' => $cancelledTutors,
            ]);
        }

        return response()->json($stats);
    }
}
