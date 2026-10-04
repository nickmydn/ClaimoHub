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
        $query = MstVoucherProduct::where('mvp_code', $code);

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
            'mvp_provider'      => $data['mvp_provider'],
            'mvp_code'          => $data['mvp_code'],
            'mvp_name'          => $data['mvp_name'],
            'mvp_denomination'  => $data['mvp_denomination'],
            'mvp_stock'         => $data['mvp_stock'] ?? $mstVoucherProduct->mvp_stock,
            'is_active'        => $data['is_active'] ?? $mstVoucherProduct->is_active,
        ]);

        return $mstVoucherProduct->refresh();
    }

    public function deleteData(MstVoucherProduct $mstVoucherProduct): bool
    {
        return $mstVoucherProduct->delete();
    }

    public function increaseStock(
        MstVoucherProduct $voucherProduct
    ): void {
        $voucherProduct->increment('mvp_stock');
    }

    public function decreaseStock(
        MstVoucherProduct $voucherProduct
    ): void {
        $voucherProduct->decrement('mvp_stock');
    }
}
