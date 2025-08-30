<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * GET /api/dashboard/{userId}/{role}
     * Auth required. Users can only see their own dashboard unless admin.
     */
    public function getDashboardStats($userId, $role)
    {
        $auth = Auth::user();
        if (!$auth) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        if ($auth->role !== 'admin' && (int)$auth->id !== (int)$userId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $stats = $this->dashboardService->getDashboardStats((int)$userId, (string)$role);

        if (!empty($stats['error'])) {
            return response()->json(['message' => $stats['error']], 400);
        }

        return response()->json($stats, 200);
    }
}
