<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TransRewardModel;
use App\Services\TranRewardService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\RewardEligibilityService;

class TransRewardController extends Controller
{
    public function __construct(protected TranRewardService $service, protected RewardEligibilityService $rewardService) {
        $this->service = $service;
        $this->rewardService = $rewardService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $result = $this->service->getData();

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'trc_user_id' => [
                'required',
                'exists:users,id',
            ],

            'trc_merchant_id' => [
                'required',
                'exists:merchants,id',
            ],

            'trc_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'trc_status' => [
                'sometimes',
                'in:pending,completed,cancelled',
            ],

            'trc_trx_date' => [
                'sometimes',
                'date',
            ],
        ]);

        $data = [
            'trc_user_id' => $validated['trc_user_id'],
            'trc_merchant_id' => $validated['trc_merchant_id'],
            'trc_trx_number' => $this->genTrxNo(),
            'trc_amount' => $validated['trc_amount'],
            'trc_status' => $validated['trc_status'] ?? 'completed',
            'trc_trx_date' => $validated['trc_trx_date'] ?? now(),
        ];

        $result = $this->service->storeData($data);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.',
            'data' => $result->load([
                'user',
                'merchant',
            ]),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(TransRewardModel $transRewardModel)
    {
        $result = $this->service->getDataById($transRewardModel->id);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransRewardModel $transRewardModel)
    {
        $validated = $request->validate([
            'trc_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'trc_status' => [
                'required',
                'in:pending,completed,cancelled',
            ],

            'trc_trx_date' => [
                'required',
                'date',
            ],
        ]);

        $result = $this->service->updateData($transRewardModel, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Transaction updated successfully.',
            'data' => $result->load([
                'user',
                'merchant',
            ]),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransRewardModel $transRewardModel)
    {
        $this->service->deleteData($transRewardModel);

        return response()->json([
            'success' => true,
            'message' => 'Transaction deleted successfully.',
        ]);
    }

    public function storeCustTrx(Request $request)
    {
        $validated = $request->validate([
            'trc_merchant_id' => [
                'required',
                'exists:mst_merchant,id',
            ],

            'trc_amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'trc_trx_date' => [
                'sometimes',
                'date',
            ],
        ]);

        $data = [
            'trc_user_id' => $request->user()->id,

            'trc_merchant_id' => $validated['trc_merchant_id'],

            'trc_trx_no' => $this->genTrxNo(),

            'trc_amount' => $validated['trc_amount'],

            'trc_status' => 'completed',

            'trc_trx_date' => $validated['trc_trx_date'] ?? now(),
        ];

        $result = $this->service->storeData($data);

        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.',
            'data' => $result->load([
                'merchant',
            ]),
        ], 201);
    }

    public function myTrx(Request $request)
    {
        $result = $this->service->getMyTransactions($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function claimReward( Request $request, TransRewardModel $transRewardModel
    ) {
        if (
        $transRewardModel->user_id
            !== $request->user()->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden.',
            ], 403);
        }

        $reward =
            $this->rewardService
                ->checkAndCreateReward(
                $transRewardModel
                );

        if (!$reward) {
            return response()->json([
                'success' => false,
                'message' =>
                    'Transaction is not eligible for reward.',
                'data' => null,
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Reward created successfully.',
            'data' => $reward->load([
                'campaign',
                'voucherProduct',
            ]),
        ]);
    }

    private function genTrxNo(): string
    {
        do {
            $number = 'TRX-' . now()->format('YmdHis')
                . '-' . strtoupper(Str::random(5));
        } while (
            TransRewardModel::where('trc_trx_no', $number)->exists()
        );

        return $number;
    }
}
