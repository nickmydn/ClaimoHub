<?php

namespace App\Repositories;

use App\Models\VoucherRedemption;
class VoucherRedemptionRepository
{
    public function storeData(
        array $data
    ): VoucherRedemption {

        return VoucherRedemption::create($data);
    }

    public function findByRewardOrderTransId(
        int $rewardOrderTransId
    ): ?VoucherRedemption {

        return VoucherRedemption::where(
            'tvr_reward_order_trans_id',
            $rewardOrderTransId
        )->first();
    }

    public function findByExternalReference(
        string $externalReference
    ): ?VoucherRedemption {

        return VoucherRedemption::where(
            'tvr_external_reference',
            $externalReference
        )->first();
    }

    public function updateData(
        VoucherRedemption $voucherRedemption,
        array $data
    ): VoucherRedemption {

        $voucherRedemption->update($data);

        return $voucherRedemption->refresh();
    }

    public function getByUserId(
        int $userId
    ) {

        return VoucherRedemption::with([
            'rewardOrderTrans',
            'voucherProduct',
        ])
            ->where(
                'tvr_user_id',
                $userId
            )
            ->latest()
            ->get();
    }
}
