<?php

namespace App\Repositories;

use App\Models\MstMerchant;
use Illuminate\Database\Eloquent\Collection;

class MasterMerchantRepository{
    public function findData(): Collection {
        return MstMerchant::get();
    }

    public function findDataId(int $id): ?MstMerchant {
        return MstMerchant::find($id);
    }

    public function getDetail(MstMerchant $mstMerchant): MstMerchant{
        return $mstMerchant->load([
            'mst_distributor',
            'mst_products',
        ]);
    }

    public function isCodeExists(int $distributorId, string $code): bool{
        return MstMerchant::where('mm_distributor_id', $distributorId)
            ->where('mm_code', $code)
            ->exists();
    }

    public function storeData(array $data): MstMerchant{
        return MstMerchant::create($data);
    }

    public function updateData(MstMerchant $mstMerchant, array $data): MstMerchant{
        $mstMerchant->update([
            'md_name' => $data['md_name'],
            'md_email' => $data['md_email'] ?? null,
            'md_phone' => $data['md_phone'] ?? null,
            'md_address' => $data['md_address'] ?? null,
            'is_active' => $data['is_active'] ?? $mstMerchant->is_active,
        ]);

        return $mstMerchant->refresh();
    }

    public function deleteData(MstMerchant $mstMerchant): bool{
        if ($mstMerchant->mst_merchants()->exists()) {
            throw new \Exception(
                'Merchant cannot be deleted because it has Distributor and Product.'
            );
        }
        return $mstMerchant->delete();
    }
}
