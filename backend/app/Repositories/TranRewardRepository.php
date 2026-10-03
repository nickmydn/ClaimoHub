<?php

namespace App\Repositories;

use App\Models\TransRewardModel;
use Illuminate\Database\Eloquent\Collection;

class TranRewardRepository
{
    public function getData(): Collection
    {
        return TransRewardModel::with([
            'user',
            'merchant',
        ])
            ->latest()
            ->get();
    }

    public function getDataById(int $id): ?TransRewardModel
    {
        return TransRewardModel::with([
            'user',
            'merchant',
        ])->find($id);
    }

    public function storeData(array $data): TransRewardModel
    {
        return TransRewardModel::create($data);
    }

    public function updateData(
        TransRewardModel $transRewardModel,
        array $data
    ): bool {
        return $transRewardModel->update($data);
    }

    public function deleteData(
        TransRewardModel $transRewardModel
    ): bool {
        return $transRewardModel->delete();
    }

    public function existsByTrxNo(
        string $trxNo
    ): bool {
        return TransRewardModel::where(
            'trc_trx_no',
            $trxNo
        )->exists();
    }

    public function getByUserId(int $userId): Collection
    {
        return TransRewardModel::with([
            'merchant',
        ])
            ->where('trc_user_id', $userId)
            ->latest()
            ->get();
    }
}
