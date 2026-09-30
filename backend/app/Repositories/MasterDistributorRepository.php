<?php

namespace App\Repositories;

use App\Models\MstDistributor;
use Illuminate\Database\Eloquent\Collection;

class MasterDistributorRepository{
    public function findData(): Collection {
        return MstDistributor::get();
    }

    public function findDataId(int $id): ?MstDistributor {
        return MstDistributor::find($id);
    }

    public function getWithMerchants(MstDistributor $distributor): MstDistributor{
        return $distributor->load('mst_merchants');
    }

    public function storeData(array $data): MstDistributor{
        return MstDistributor::create($data);
    }

    public function updateData(MstDistributor $distributor, array $data): MstDistributor
    {
        $distributor->update([
            'md_name' => $data['md_name'],
            'md_email' => $data['md_email'] ?? null,
            'md_phone' => $data['md_phone'] ?? null,
            'md_address' => $data['md_address'] ?? null,
            'is_active' => $data['is_active'] ?? $distributor->is_active,
        ]);

        return $distributor->refresh();
    }

    public function deleteData(MstDistributor $distributor): bool
    {
        if ($distributor->mst_merchants()->exists()) {
            throw new \Exception(
                'Distributor cannot be deleted because it has merchants.'
            );
        }
        return $distributor->delete();
    }
}
