<?php

namespace App\Http\Controllers;
use App\Services\ApplicationService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(ApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    
    public function index()
    {
        $applications = $this->applicationService->getAllApplications();
        return response()->json($applications);
    }
    public function getTutorStats(Request $request, $userId)
    {
        $stats = $this->applicationService->getTutorStats($userId);
        if (!$stats) {
            return response()->json(['message' => 'Tutor not found'], 404);
        }

        return response()->json($stats);
    }
    public function getLearnerStats(Request $request, $userId)
    {
        $stats = $this->applicationService->getLearnerStats($userId);
        if (!$stats) {
            return response()->json(['message' => 'Learner not found'], 404);
        }

        return response()->json($stats);
    }

    public function store(Request $request)
    {
        $result = $this->applicationService->createApplication($request);

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json(['message' => $result['message']], $result['status']);
    }
}

