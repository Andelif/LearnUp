<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboardStats($userId, $role)
    {
        // Get the stats based on role
        if ($role === 'tutor') {
            $stats = $this->dashboardService->getTutorDashboardStats($userId);
            return response()->json($stats);
        } elseif ($role === 'learner') {
            $stats = $this->dashboardService->getLearnerDashboardStats($userId);
            return response()->json($stats);
        }

        // If role is not recognized, return an empty array
        return response()->json([]);
    }
}
