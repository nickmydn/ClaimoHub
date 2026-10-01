<?php

namespace App\Repositories;

use App\Models\MstProduct;
use Illuminate\Database\Eloquent\Collection;

class MasterProductRepository{
    public function findData(): Collection {
        return MstProduct::get();
    }

    public function findDataId(int $id): ?MstProduct {
        return MstProduct::find($id);
    }

    public function getDetail(MstProduct $mstProduct): MstProduct{
        return $mstProduct->load([
            'mst_merchant'
        ]);
    }

    public function isCodeExists(int $merchantId, string $code, ?int $prd_id = null): bool{
        $query = MstProduct::where('mp_merchant_id', $merchantId)
            ->where('mp_code', $code);

        if ($prd_id !== null) {
            $query->where('id', '!=', $prd_id);
        }

        return $query->exists();
    }

    public function storeData(array $data): MstProduct
    {
        return MstProduct::create($data)
            ->load('mst_merchant');
    }

    public function updateData(MstProduct $mstProduct, array $data): MstProduct{
        $mstProduct->update([
            'mp_merchant_id' => $data['mp_merchant_id'] ?? $mstProduct->mp_merchant_id,
            'mp_code'        => $data['mp_code'] ?? $mstProduct->mp_code,
            'mp_name'        => $data['mp_name'],
            'mp_price'       => $data['mp_price'],
            'is_active'      => $data['is_active'] ?? $mstProduct->is_active,
        ]);

        return $mstProduct->refresh()->load('mst_merchant');
    }

    public function deleteData(MstProduct $mstProduct): bool
    {
        return $mstProduct->delete();
    }

}
