<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RewardOrderTrans;
use App\Services\RewardOrderTransService;
use Illuminate\Http\Request;

class ProcessRewardController extends Controller
{
    public function __construct(
        protected RewardOrderTransService
        $rewardOrderTransService
    ) {}

    public function redeem(
        Request $request,
        RewardOrderTrans $rewardOrderTrans
    ) {

        if ($rewardOrderTrans->transaction->trc_user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not allowed to redeem this reward.',
            ], 403);
        }

        if (!in_array($rewardOrderTrans->status, ['pending'])) {
            return response()->json([
                'success' => false,
                'message' => 'Reward has already been processed.',
            ], 422);
        }

        $result = $this->rewardOrderTransService->redeem($rewardOrderTrans, $request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Voucher redeemed successfully.',
            'data' => $result,
        ]);
    }

    public function myRedemptions(Request $request) {
        $result = $this->rewardOrderTransService->getMyRedemptions($request->user()->id);

        return response()->json([
            'success' => true,
            'message' => 'Voucher redemptions retrieved successfully.',
            'data' => $result,
        ]);
    }
}
