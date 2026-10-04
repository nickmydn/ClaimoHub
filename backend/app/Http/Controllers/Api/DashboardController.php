<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    public function adminDashboard(): JsonResponse
    {
        try {

            $data = $this->dashboardService
                ->getAdminDashboard();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard data retrieved successfully.',
                'data' => $data,
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard data.',
            ], 500);
        }
    }
}
