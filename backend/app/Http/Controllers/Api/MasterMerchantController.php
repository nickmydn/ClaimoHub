<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstMerchant;
use App\Services\MasterMerchantService;

class MasterMerchantController extends Controller
{
    public function __construct(
        protected MasterMerchantService $service
    ) {}

    public function index()
    {
        $result = $this->service->getAllData();

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mm_distributor_id' => ['required', 'exists:mst_distributor,id'],
            'mm_code' => ['required', 'string', 'max:30'],
            'mm_name' => ['required', 'string', 'max:150'],
            'mm_email' => ['nullable', 'email', 'max:150'],
            'mm_phone' => ['nullable', 'string', 'max:30'],
            'mm_address' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $result = $this->service->storeData($validated);

        return response()->json([
            'success' => true,
            'message' => 'Merchant created successfully.',
            'data' => $result,
        ], 201);
    }

    public function show(MstMerchant $mstMerchant)
    {
        $result = $this->service->getDataDetail($mstMerchant);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    public function update(Request $request, MstMerchant $mstMerchant)
    {
        $validated = $request->validate([
            'mm_name' => ['required', 'string', 'max:150'],
            'mm_email' => ['nullable', 'email', 'max:150'],
            'mm_phone' => ['nullable', 'string', 'max:30'],
            'mm_address' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $result = $this->service->updateData($mstMerchant, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Merchant updated successfully.',
            'data' => $result,
        ]);
    }

    public function destroy(MstMerchant $mstMerchant)
    {
        $this->service->deleteData($mstMerchant);

        return response()->json([
            'success' => true,
            'message' => 'Merchant deleted successfully.',
        ]);
    }
}
