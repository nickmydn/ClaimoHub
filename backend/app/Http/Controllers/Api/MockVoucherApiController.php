<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MockVoucherApiController extends Controller
{
    public function issue(Request $request)
    {
        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:100',
            ],

            'product_code' => [
                'required',
                'string',
                'max:100',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:1',
            ],
        ]);

        return response()->json([
            'success' => true,

            'message' =>
                'Voucher issued successfully.',

            'reference' =>
                $validated['reference'],

            'voucher_code' =>
                strtoupper(
                    $validated['product_code']
                    . '-'
                    . Str::upper(
                        Str::random(10)
                    )
                ),

            'amount' =>
                $validated['amount'],

            'provider' =>
                'Mock Voucher Provider',

            'issued_at' =>
                now(),
        ]);
    }
}
