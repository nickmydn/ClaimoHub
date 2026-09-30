<?php

namespace App\Services;

use App\Models\MstMerchant;
use App\Repositories\MasterMerchantRepository;
use Illuminate\Support\Facades\DB;
use App\Logs;

class MasterMerchantService{
    protected Logs $logs;
    public function __construct( protected MasterMerchantRepository $repo){
        $sectionName = "Master_Merchant_";
        $this->logs = new Logs( $sectionName.date('Ymd'));
        $this->logs->write("START", "===");
    }

   public function getAllData(){
        return $this->repo->findData();
    }

    public function getDataDetail(MstMerchant $mstMerchant){
          return $this->repo->getDetail($mstMerchant);
    }

    public function storeData(array $data)
    {
        if ($this->repo->isCodeExists(
            $data['mm_distributor_id'],
            $data['mm_code']
        )) {
            throw new \Exception(
                'Merchant code already exists for this distributor.'
            );
        }
        try {
            $result = DB::transaction(function() use ($data){
                return $this->repo->storeData($data);
            });

            $this->logs->write("Master Merchant created", "'merchant_id' => {$result->id} || 'code' => {$result->mm_code}");

            return $result;

        } catch (\Throwable $e) {
            $this->logs->write("Failed to create Master Merchant", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function updateData(MstMerchant $mstMerchant, array $data)
    {
        try {
            $result = DB::transaction(function() use ($mstMerchant, $data){
                return $this->repo->updateData($mstMerchant, $data);
            });

            $this->logs->write("Master Merchant updated", "'merchant_id' => {$result->id} || 'code' => {$result->mm_code}");

            return $result;
        }catch (\Throwable $e) {
            $this->logs->write("Failed to update Master Merchant", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteData(MstMerchant $mstMerchant)
    {
        try {
            $id = $mstMerchant->id;
            $code = $mstMerchant->mm_code;

            DB::transaction(function () use ($mstMerchant) {
                $this->repo->deleteData($mstMerchant);
            });

            $this->logs->write(
                "Master Merchant deleted",
                "'merchant_id' => {$id} || 'code' => {$code}"
            );

            return true;
        }catch (\Throwable $e) {
            $this->logs->write("Failed to delete Master Merchant", "'error' => " . $e->getMessage());
            throw $e;
        }
    }
}
