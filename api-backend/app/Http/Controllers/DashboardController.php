<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboardStats($userId, $role)
    {
        if ($role === 'tutor') {



            
            $appliedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE tutor_id = 
                                        (SELECT TutorID FROM tutors WHERE user_id = ?)", [$userId])[0]->count;

            
            $shortlistedJobs = DB::select("SELECT COUNT(*) as count FROM applications WHERE matched = 1 AND tutor_id = 
                                            (SELECT TutorID FROM tutors WHERE user_id = ?)", [$userId])[0]->count;

            return response()->json([
                'appliedJobs' => $appliedJobs,
                'shortlistedJobs' => $shortlistedJobs,
            ]);



        } elseif ($role === 'learner') {



            
            $appliedRequests = DB::select("SELECT COUNT(*) as count FROM tuition_requests WHERE LearnerID = 
                                            (SELECT LearnerID FROM learners WHERE user_id = ?)", [$userId])[0]->count;

            
            $shortlistedTutors = DB::select("SELECT COUNT(*) as count FROM applications WHERE learner_id = 
                                                (SELECT LearnerID FROM learners WHERE user_id = ?) AND matched = 1", [$userId])[0]->count;

            return response()->json([
                'appliedRequests' => $appliedRequests,
                'shortlistedTutors' => $shortlistedTutors,
            ]);
        }

        return response()->json(['error' => 'Invalid role'], 400);



    }
}
