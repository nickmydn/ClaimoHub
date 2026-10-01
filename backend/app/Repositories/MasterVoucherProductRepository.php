<?php

namespace App\Repositories;

use App\Models\MstVoucherProduct;
use Illuminate\Support\Collection;

class MasterVoucherProductRepository
{
    public function findData(): Collection
    {
        return MstVoucherProduct::latest()->get();
    }

    public function findDataId(int $id): ?MstVoucherProduct
    {
        return MstVoucherProduct::find($id);
    }

    public function getDetail(MstVoucherProduct $mstVoucherProduct): MstVoucherProduct
    {
        return $mstVoucherProduct->load([
            'mst_rewards',
        ]);
    }

    public function isCodeExists(
        string $code,
        ?int $voucherProductId = null
    ): bool {
        $query = MstVoucherProduct::where('mr_code', $code);

        if ($voucherProductId !== null) {
            $query->where('id', '!=', $voucherProductId);
        }

        return $query->exists();
    }

    public function storeData(array $data): MstVoucherProduct
    {
        return MstVoucherProduct::create($data);
    }

    public function updateData(
        MstVoucherProduct $mstVoucherProduct,
        array $data
    ): MstVoucherProduct {
        $mstVoucherProduct->update([
            'mr_provider'      => $data['mr_provider'],
            'mr_code'          => $data['mr_code'],
            'mr_name'          => $data['mr_name'],
            'mr_denomination'  => $data['mr_denomination'],
            'mr_stock'         => $data['mr_stock'] ?? $mstVoucherProduct->mr_stock,
            'is_active'        => $data['is_active'] ?? $mstVoucherProduct->is_active,
        ]);

        return $mstVoucherProduct->refresh();
    }

    public function deleteData(MstVoucherProduct $mstVoucherProduct): bool
    {
        return $mstVoucherProduct->delete();
    }
}
