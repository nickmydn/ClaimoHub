<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ExternalVoucherService
{
    public function issueVoucher(
        string $reference,
        string $productCode,
        float $amount
    ): array {

        $response = Http::timeout(10)
            ->retry(2, 200)
            ->post(
                config('services.voucher_api.url')
                . '/vouchers/issue',
                [
                    'reference' => $reference,
                    'product_code' => $productCode,
                    'amount' => $amount,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'External voucher API request failed.'
            );
        }

        $result = $response->json();

        if (
            !isset($result['success'])
            || !$result['success']
        ) {
            throw new RuntimeException(
                $result['message']
                ?? 'Voucher issuance failed.'
            );
        }

        return $result;
    }
}
