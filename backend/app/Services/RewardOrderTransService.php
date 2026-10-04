<?php

namespace App\Services;

use App\Models\RewardOrderTrans;
use App\Repositories\RewardOrderTransRepository;
use App\Repositories\VoucherRedemptionRepository;
// use App\Models\VoucherRedemption;
use App\Logs;
use App\Repositories\RewardRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use App\Repositories\MasterVoucherProductRepository;

class RewardOrderTransService
{
    protected Logs $logs;

    public function __construct(
        protected RewardOrderTransRepository $rewardOrderTransRepository,
        protected VoucherRedemptionRepository $voucherRedemptionRepository,
        protected ExternalVoucherService $externalVoucherService,
        protected RewardRepository $reward_repository,
        protected MasterVoucherProductRepository $mstVoucherProductRepository
    ) {
        $this->rewardOrderTransRepository = $rewardOrderTransRepository;

        $this->voucherRedemptionRepository = $voucherRedemptionRepository;

        $this->externalVoucherService = $externalVoucherService;

        $this->reward_repository = $reward_repository;

        $this->mstVoucherProductRepository = $mstVoucherProductRepository;

        $this->logs = new Logs("Reward_Order_Trans_". date('Ymd'));
        $this->logs->write("START","===");
    }

    public function redeem(RewardOrderTrans $rewardOrderTrans, int $userId) {
        try {
            /** 1. Buat redemption pending*/
            $redemption = DB::transaction(function () use ($rewardOrderTrans, $userId) {
                $existingRedemption = $this->voucherRedemptionRepository->findByRewardOrderTransId($rewardOrderTrans->id);
                    if ($existingRedemption) {
                        return $existingRedemption;
                    }

                    return $this->voucherRedemptionRepository->storeData(
                        [
                            'tvr_reward_order_trans_id' => $rewardOrderTrans->id,
                            'tvr_user_id' => $userId,
                            'tvr_voucher_product_id' => $rewardOrderTrans ->voucher_product_id,
                            'tvr_redemption_no' => $this->generateRedemptionNumber(),
                            'tvr_external_reference' => $this->generateExternalReference(),
                            'tvr_amount' => $rewardOrderTrans ->reward_amount,
                            'tvr_status' =>'pending',
                        ]);
                }
            );

            /** 2. Kalau sudah success, jangan issue voucher lagi*/
            if ($redemption->tvr_status === 'success') {
                return $redemption;
            }

            /** 3. Call external voucher API*/
            $voucherProduct = $rewardOrderTrans->voucherProduct;
            $result = $this->externalVoucherService->issueVoucher($redemption->tvr_external_reference,$voucherProduct->mvp_code,(float) $redemption->tvr_amount);

            /** 4. Update redemption + reward */
            $redemption = DB::transaction(function () use ($redemption, $rewardOrderTrans,$result) {
                    $redemption = $this->voucherRedemptionRepository->updateData($redemption,
                                [
                                    'tvr_voucher_code' =>$result['voucher_code'],
                                    'tvr_status' =>'success',
                                    'tvr_redeemed_at' =>now(),
                                ]);

                    $rewardOrderTrans->update(['status' => 'claimed',]);

                    return $redemption;
                }
            );

            $this->logs->write("Voucher redeemed","'redemption_id' => ". "{$redemption->id} || ". "'reward_order_trans_id' => ". "{$rewardOrderTrans->id}");

            return $redemption;

        } catch (Throwable $e) {
            /** 5. Tandai redemption failed */
            if (isset($redemption) && $redemption) {
                DB::transaction(function () use ($redemption, $e) {
                    $this->voucherRedemptionRepository
                        ->updateData($redemption,['tvr_status' =>'failed', 'tvr_failure_reason' => $e->getMessage(),]);
                });
            }

            $this->logs->write(
                "Failed to redeem voucher",
                "'reward_order_trans_id' => "
                . "{$rewardOrderTrans->id} || "
                . "'error' => "
                . $e->getMessage()
            );

            /** Return quota + stock*/
            $this->releaseRewardReservation($rewardOrderTrans);

            $this->logs->write(
                "Failed to redeem voucher",
                "'reward_order_trans_id' => "
                . "{$rewardOrderTrans->id} || "
                . "'error' => "
                . $e->getMessage()
            );
            throw $e;
        }
    }

    public function getMyRedemptions(int $userId) {
        return $this->voucherRedemptionRepository->getByUserId($userId);
    }

    protected function releaseRewardReservation(
        RewardOrderTrans $rewardOrderTrans
    ): void {

        DB::transaction(function () use ($rewardOrderTrans) {
            $reward = $rewardOrderTrans->campaign;
            $voucherProduct = $rewardOrderTrans->voucherProduct;

            /** Return reward quota*/
            $this->reward_repository->increaseQuota($reward);

            /** Return voucher stock*/
            $this->mstVoucherProductRepository->increaseStock($voucherProduct);

            /** Mark reward as failed*/
            $rewardOrderTrans->update(['status' => 'failed',]);
        });
    }

    protected function generateRedemptionNumber(): string {
        return 'RDM-'. date('YmdHis'). '-'. Str::upper(Str::random(6));
    }

    protected function generateExternalReference(): string {
        return 'EXT-'. date('YmdHis'). '-'. Str::upper(Str::random(6));
    }
}
