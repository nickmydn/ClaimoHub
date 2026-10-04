<?php

namespace App\Repositories;

use App\Models\MstReward;
use Illuminate\Support\Collection;

class RewardRepository
{
    public function findData(): Collection
    {
        return MstReward::with('mst_voucherPrd')
            ->latest()
            ->get();
    }

    public function findDataId(int $id): ?MstReward
    {
        return MstReward::find($id);
    }

    public function getDetail(MstReward $mstReward): MstReward
    {
        return $mstReward->load([
            'mst_voucherPrd',
        ]);
    }

    public function isCodeExists(
        string $code,
        ?int $rewardId = null
    ): bool {
        $query = MstReward::where('mr_code', $code);

        if ($rewardId !== null) {
            $query->where('id', '!=', $rewardId);
        }

        return $query->exists();
    }
    public function storeData(array $data): MstReward
    {
        return MstReward::create($data)
            ->load('mst_voucherPrd');
    }

    public function updateData(
        MstReward $mstReward,
        array $data
    ): MstReward {
        $mstReward->update([
            'mr_voucher_product_id' => $data['mr_voucher_product_id'],
            'mr_code'               => $data['mr_code'],
            'mr_name'               => $data['mr_name'],
            'mr_min_transaction'    => $data['mr_min_transaction'],
            'mr_quota'              => $data['mr_quota'],
            'mr_start_date'         => $data['mr_start_date'],
            'mr_end_date'           => $data['mr_end_date'],
            'is_active'             => $data['is_active'] ?? $mstReward->is_active,
        ]);

        return $mstReward->refresh()
            ->load('mst_voucherPrd');
    }

    public function deleteData(MstReward $mstReward): bool
    {
        return $mstReward->delete();
    }

    public function getEligibleRewards(
        float $amount,
        $transactionDate
    ): Collection {
        return MstReward::with('mst_voucherPrd')
            ->where('is_active', true)
            ->where('mr_min_transaction', '<=', $amount)
            ->whereDate(
                'mvp_start_date',
                '<=',
                $transactionDate
            )
            ->whereDate(
                'mvp_end_date',
                '>=',
                $transactionDate
            )
            ->where('mvp_quota', '>', 0)
            ->whereHas('mst_voucherPrd', function ($query) {
                $query
                    ->where('is_active', true)
                    ->where('mvp_stock', '>', 0);
            })
            ->orderBy(
                'mvp_min_transaction',
                'desc'
            )
            ->get();
    }

    public function findForUpdate(
        int $id
    ): ?MstReward {
        return MstReward::with('mst_voucherPrd')
            ->where('id', $id)
            ->lockForUpdate()
            ->first();
    }

    public function increaseQuota(
        MstReward $reward
    ): void {
        $reward->increment('mr_quota');
    }

    public function decreaseQuota(
        MstReward $mstReward
    ): bool {
        return $mstReward->decrement('mvp_quota');
    }
}
