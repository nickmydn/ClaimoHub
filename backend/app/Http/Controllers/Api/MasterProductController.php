<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstProduct;
use App\Services\MasterProductService;

class MasterProductController extends Controller
{
    public function __construct(
        protected MasterProductService $service
    ) {}
    public function index()
    {
        $result = $this->service->getAllData();

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
            'mp_merchant_id' => ['required', 'exists:mst_merchant,id'],
            'mp_code' => ['required', 'string', 'max:30'],
            'mp_name' => ['required', 'string', 'max:150'],
            'mp_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $result = $this->service->storeData($validated);

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $result,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MstProduct $mstProduct)
    {
        $result = $this->service->getDataDetail($mstProduct);

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MstProduct $mstProduct)
    {
        $validated = $request->validate([
            'mp_merchant_id' => ['required', 'exists:mst_merchant,id'],
            'mp_name' => ['required', 'string', 'max:150'],
            'mp_price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $result = $this->service->updateData($mstProduct, $validated);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $result,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MstProduct $mstProduct)
    {
        $this->service->deleteData($mstProduct);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.',
        ]);
    }
}
