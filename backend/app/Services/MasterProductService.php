<?php

namespace App\Services;

use App\Models\MstProduct;
use App\Repositories\MasterProductRepository;
use Illuminate\Support\Facades\DB;
use App\Logs;

class MasterProductService{
    protected Logs $logs;
    public function __construct( protected MasterProductRepository $repo){
        $sectionName = "Master_Product_";
        $this->logs = new Logs( $sectionName.date('Ymd'));
        $this->logs->write("START", "===");
    }

   public function getAllData(){
        return $this->repo->findData();
    }

    public function getDataDetail(MstProduct $mstProduct){
          return $this->repo->getDetail($mstProduct);
    }

    public function storeData(array $data)
    {
        if ($this->repo->isCodeExists(
            $data['mp_merchant_id'],
            $data['mp_code']
        )) {
            throw new \Exception(
                'Product code already exists for this merchant.'
            );
        }

        try {
            $result = DB::transaction(function() use ($data){
                return $this->repo->storeData($data);
            });

            $this->logs->write("Master Product created", "'product_id' => {$result->id} || 'code' => {$result->mm_code}");

            return $result;

        } catch (\Throwable $e) {
            $this->logs->write("Failed to create Master Product", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function updateData(MstProduct $mstProduct, array $data)
    {
        if ($this->repo->isCodeExists(
            $data['mp_merchant_id'],
            $data['mp_code'],
            $mstProduct->id
        )) {
            throw new \Exception(
                'Product code already exists for this merchant.'
            );
        }


        try {
            $result = DB::transaction(function() use ($mstProduct, $data){
                return $this->repo->updateData($mstProduct, $data);
            });

            $this->logs->write("Master Merchant updated", "'merchant_id' => {$result->id} || 'code' => {$result->mm_code}");

            return $result;
        }catch (\Throwable $e) {
            $this->logs->write("Failed to update Master Merchant", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteData(MstProduct $mstProduct)
    {
        try {
            $id = $mstProduct->id;
            $code = $mstProduct->mm_code;

            DB::transaction(function () use ($mstProduct) {
                $this->repo->deleteData($mstProduct);
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
