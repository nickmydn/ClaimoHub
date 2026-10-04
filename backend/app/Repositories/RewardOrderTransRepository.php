<?php

namespace App\Repositories;

use App\Models\RewardOrderTrans;

class RewardOrderTransRepository
{
    public function findByTransactionId(
        int $transactionId
    ): ?RewardOrderTrans {
        return RewardOrderTrans::with([
            'campaign',
            'voucherProduct',
        ])
            ->where(
                'transaction_id',
                $transactionId
            )
            ->first();
    }

    public function storeData(
        array $data
    ): RewardOrderTrans {
        return RewardOrderTrans::create($data);
    }

    public function findById(
        int $id
    ): ?RewardOrderTrans {

        return RewardOrderTrans::with([
            'campaign',
            'voucherProduct',
        ])
        ->find($id);
    }
}
