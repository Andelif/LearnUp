<?php

namespace App\Http\Controllers;

use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApplicationController extends Controller
{
    protected ApplicationService $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    /**
     * GET /api/applications
     * - Admin: all applications
     * - Tutor: own applications
     * - Learner: applications on their tuition requests
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        if ($user->role === 'admin') {
            return response()->json($this->applicationService->getAllApplications(), 200);
        }
        if ($user->role === 'tutor') {
            return response()->json($this->applicationService->getApplicationsByTutorUser($user->id), 200);
        }
        if ($user->role === 'learner') {
            return response()->json($this->applicationService->getApplicationsByLearnerUser($user->id), 200);
        }
        return response()->json(['message' => 'Forbidden'], 403);
    }

    /** GET /api/tutor/{userId}/stats */
    public function getTutorStats(Request $request, $userId)
    {
        $stats = $this->applicationService->getTutorStats((int)$userId);
        if (!$stats) {
            return response()->json(['message' => 'Tutor not found'], 404);
        }
        return response()->json($stats, 200);
    }

    /** GET /api/learner/{userId}/stats */
    public function getLearnerStats(Request $request, $userId)
    {
        $stats = $this->applicationService->getLearnerStats((int)$userId);
        if (!$stats) {
            return response()->json(['message' => 'Learner not found'], 404);
        }
        return response()->json($stats, 200);
    }

    /** POST /api/applications  (auth: tutor) */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // NOTE: 'tution_id' (without the i) to match your DB column
            'tution_id' => 'required|integer|min:1',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $result = $this->applicationService->createApplication($validator->validated());

        if (!empty($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status'] ?? 400);
        }
        return response()->json(['message' => $result['message'], 'data' => $result['data']], $result['status'] ?? 201);
    }

    /**
     * GET /api/applications/check/{tution_id}
     * Returns { applied: true|false } for current tutor.
     */
    public function checkApplication(Request $request, $tution_id)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Unauthorized'], 401);

        // map current user -> tutors.TutorID (not users.id)
        $tutorId = DB::table('tutors')->where('user_id', $user->id)->value('TutorID');
        if (!$tutorId) return response()->json(['applied' => false], 200);

        $applied = DB::table('applications')
            ->where('tution_id', (int)$tution_id)
            ->where('tutor_id', $tutorId)
            ->exists();

        return response()->json(['applied' => (bool)$applied], 200);
    }
}
