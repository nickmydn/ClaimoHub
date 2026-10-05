<?php

namespace App\Repositories;

use App\Models\MstDistributor;
use App\Models\MstMerchant;
use App\Models\MstVoucherProduct;
use App\Models\RewardOrderTrans;
use App\Models\TransRewardModel;

class DashboardRepository
{
    public function getTotalDistributors(): int
    {
        return MstDistributor::count();
    }

    public function getTotalMerchants(): int
    {
        return MstMerchant::count();
    }

    public function getTotalTransactions(): int
    {
        return TransRewardModel::count();
    }

    public function getTotalVoucherStock(): int
    {
        return (int) MstVoucherProduct::sum('mvp_stock');
    }

    public function getTotalRewardsClaimed(): int
    {
        return RewardOrderTrans::whereIn(
            'status',
            ['pending', 'claimed']
        )->count();
    }

    public function getRecentTransactions(int $limit = 5)
    {
        return TransRewardModel::with([
            'merchant',
            'user',
        ])
            ->latest('trc_trx_date')
            ->limit($limit)
            ->get();
    }

    public function getTransactionChart(): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $transactions = TransRewardModel::query()
            ->selectRaw("
                DATE(trc_trx_date) as date,
                COUNT(*) as total_transactions,
                SUM(trc_amount) as total_amount
            ")
            ->whereBetween('trc_trx_date', [$startDate, $endDate])
            ->groupByRaw('DATE(trc_trx_date)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $result = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');

            $transaction = $transactions->get($date);

            $result[] = [
                'date' => $date,
                'total_transactions' => $transaction
                    ? (int) $transaction->total_transactions
                    : 0,
                'total_amount' => $transaction
                    ? (float) $transaction->total_amount
                    : 0,
            ];
        }

        return $result;
    }
}
