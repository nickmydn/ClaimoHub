<?php

namespace App\Services;

use App\Repositories\DashboardRepository;
use Illuminate\Support\Facades\Log;
use Throwable;

class DashboardService
{
    public function __construct(
        protected DashboardRepository $dashboardRepository
    ) {}

    public function getAdminDashboard(): array
    {
        try {
            return [
                'statistics' => [
                    'total_distributors' =>
                        $this->dashboardRepository
                            ->getTotalDistributors(),

                    'total_merchants' =>
                        $this->dashboardRepository
                            ->getTotalMerchants(),

                    'total_transactions' =>
                        $this->dashboardRepository
                            ->getTotalTransactions(),

                    'total_voucher_stock' =>
                        $this->dashboardRepository
                            ->getTotalVoucherStock(),

                    'total_rewards_claimed' =>
                        $this->dashboardRepository
                            ->getTotalRewardsClaimed(),
                ],

                'recent_transactions' =>
                    $this->dashboardRepository
                        ->getRecentTransactions(),
            ];
        } catch (Throwable $e) {

            Log::error('Dashboard_Admin_' . date('Y-m-d'), [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
