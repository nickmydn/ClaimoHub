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
}
