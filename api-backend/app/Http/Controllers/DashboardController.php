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
        $stats = $this->dashboardService->getDashboardStats($userId, $role);

        if (isset($stats['error'])) {
            return response()->json($stats, 400);
        }

        return response()->json($stats);
    }
}
