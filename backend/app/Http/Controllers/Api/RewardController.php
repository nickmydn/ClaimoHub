<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MstReward;
use App\Services\RewardService;
use Illuminate\Http\Request;

class RewardController extends Controller
{
    public function __construct(
        protected RewardService $service
    ) {}

    public function index()
    {
        $rewards = $this->service->getAll();

        return response()->json([
            'success' => true,
            'data' => $rewards,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mr_voucher_product_id' => [
                'required',
                'exists:mst_voucher_product,id',
            ],
            'mr_code' => [
                'required',
                'string',
                'max:50',
            ],
            'mr_name' => [
                'required',
                'string',
                'max:150',
            ],
            'mr_min_transaction' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mr_quota' => [
                'required',
                'integer',
                'min:0',
            ],
            'mr_start_date' => [
                'required',
                'date',
            ],
            'mr_end_date' => [
                'required',
                'date',
                'after_or_equal:mr_start_date',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $reward = $this->service->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Reward created successfully.',
            'data' => $reward,
        ], 201);
    }

    public function show(MstReward $mstReward)
    {
        $reward = $this->service->getDetail($mstReward);

        return response()->json([
            'success' => true,
            'data' => $reward,
        ]);
    }

    public function update(
        Request $request,
        MstReward $mstReward
    ) {
        $validated = $request->validate([
            'mr_voucher_product_id' => [
                'required',
                'exists:mst_voucher_product,id',
            ],
            'mr_code' => [
                'required',
                'string',
                'max:50',
            ],
            'mr_name' => [
                'required',
                'string',
                'max:150',
            ],
            'mr_min_transaction' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mr_quota' => [
                'required',
                'integer',
                'min:0',
            ],
            'mr_start_date' => [
                'required',
                'date',
            ],
            'mr_end_date' => [
                'required',
                'date',
                'after_or_equal:mr_start_date',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $reward = $this->service->update(
            $mstReward,
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Reward updated successfully.',
            'data' => $reward,
        ]);
    }

    public function destroy(MstReward $mstReward)
    {
        $this->service->delete($mstReward);

        return response()->json([
            'success' => true,
            'message' => 'Reward deleted successfully.',
        ]);
    }
}
