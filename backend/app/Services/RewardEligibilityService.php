<?php

namespace App\Services;

use App\Models\TransRewardModel;
use App\Models\RewardOrderTrans;
use App\Repositories\RewardRepository;
use App\Repositories\RewardOrderTransRepository;
use App\Logs;
use Illuminate\Support\Facades\DB;
use Throwable;

class RewardEligibilityService
{
    protected Logs $logs;

    public function __construct(
        protected RewardRepository $reward_repository,
        protected RewardOrderTransRepository $rewardOrderTransRepository
    ) {
        $this->reward_repository = $reward_repository;
        $this->rewardOrderTransRepository = $rewardOrderTransRepository;

        $this->logs = new Logs(
            "Reward_Eligibility_" . date('Ymd')
        );

        $this->logs->write("START", "===");
    }

    public function checkAndCreateReward(
        TransRewardModel $transRewardModel
    ): ?RewardOrderTrans {
        try {

            $result = DB::transaction(
                function () use ($transRewardModel) {

                    // 1. Cek apakah transaction
                    //    sudah punya reward
                    $existingReward =
                        $this->rewardOrderTransRepository
                            ->findByTransactionId(
                                $transRewardModel->id
                            );

                    if ($existingReward) {
                        return $existingReward;
                    }

                    // 2. Cari campaign yang eligible
                    $campaigns =
                        $this->reward_repository
                            ->getEligibleCampaigns(
                                (float) $transRewardModel->amount,
                                $transRewardModel->transaction_date
                            );

                    if ($campaigns->isEmpty()) {
                        return null;
                    }

                    // 3. Ambil campaign pertama
                    $campaign = $campaigns->first();

                    // 4. Lock campaign
                    $campaign =
                        $this->reward_repository
                            ->findForUpdate(
                                $campaign->id
                            );

                    if (!$campaign) {
                        return null;
                    }

                    // 5. Re-check quota
                    if ($campaign->quota <= 0) {
                        return null;
                    }

                    // 6. Lock voucher product
                    $voucherProduct =
                        $campaign->voucherProduct()
                            ->lockForUpdate()
                            ->first();

                    if (!$voucherProduct) {
                        return null;
                    }

                    // 7. Re-check stock
                    if (
                        !$voucherProduct->is_active
                        || $voucherProduct->stock <= 0
                    ) {
                        return null;
                    }

                    // 8. Kurangi quota
                    $campaign->decrement('quota');

                    // 9. Reserve voucher stock
                    $voucherProduct->decrement('stock');

                    // 10. Create reward
                    return $this->rewardOrderTransRepository->storeData([
                        'transaction_id' =>
                            $transRewardModel->id,

                        'campaign_id' =>
                            $campaign->id,

                        'voucher_product_id' =>
                            $voucherProduct->id,

                        'reward_amount' =>
                            $voucherProduct->denomination,

                        'status' =>
                            'pending',

                        'awarded_at' =>
                            now(),
                    ]);
                }
            );

            if ($result) {

                $this->logs->write(
                    "Reward created",
                    "'reward_id' => {$result->id} || "
                    . "'transaction_id' => "
                    . "{$transRewardModel->id} || "
                    . "'campaign_id' => "
                    . "{$result->campaign_id}"
                );
            }

            return $result;

        } catch (Throwable $e) {

            $this->logs->write(
                "Failed to create Reward",
                "'transaction_id' => "
                . "{$transRewardModel->id} || "
                . "'error' => "
                . $e->getMessage()
            );

            throw $e;
        }
    }
}
