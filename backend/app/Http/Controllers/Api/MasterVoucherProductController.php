<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MstVoucherProduct;
use App\Services\MasterVoucherProductService;

class MasterVoucherProductController extends Controller
{
    public function __construct(
        protected MasterVoucherProductService $service
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $voucherProducts = $this->service->getAll();

        return response()->json([
            'success' => true,
            'data' => $voucherProducts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'mvp_provider' => [
                'required',
                'string',
                'max:50',
            ],
            'mvp_code' => [
                'required',
                'string',
                'max:50',
            ],
            'mvp_name' => [
                'required',
                'string',
                'max:150',
            ],
            'mvp_denomination' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mvp_stock' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $voucherProduct = $this->service->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Voucher product created successfully.',
            'data' => $voucherProduct,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(MstVoucherProduct $mstVoucherProduct)
    {
        $voucherProduct = $this->service->getDetail(
            $mstVoucherProduct
        );

        return response()->json([
            'success' => true,
            'data' => $voucherProduct,
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(
        Request $request,
        MstVoucherProduct $mstVoucherProduct
    ) {
        $validated = $request->validate([
            'mvp_provider' => [
                'required',
                'string',
                'max:50',
            ],
            'mvp_code' => [
                'required',
                'string',
                'max:50',
            ],
            'mvp_name' => [
                'required',
                'string',
                'max:150',
            ],
            'mvp_denomination' => [
                'required',
                'numeric',
                'min:0',
            ],
            'mvp_stock' => [
                'sometimes',
                'integer',
                'min:0',
            ],
            'is_active' => [
                'sometimes',
                'boolean',
            ],
        ]);

        $voucherProduct = $this->service->update(
            $mstVoucherProduct,
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Voucher product updated successfully.',
            'data' => $voucherProduct,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MstVoucherProduct $mstVoucherProduct)
    {
        $this->service->delete($mstVoucherProduct);

        return response()->json([
            'success' => true,
            'message' => 'Voucher product deleted successfully.',
        ]);
    }
}
