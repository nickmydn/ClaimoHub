<?php

namespace App\Services;

use App\Models\MstDistributor;
use App\Repositories\MasterDistributorRepository;
use Illuminate\Support\Facades\DB;
use App\Logs;

class MasterDistributorService{
    protected Logs $logs;
    public function __construct( protected MasterDistributorRepository $repo){
        $sectionName = "Master_Distributor_";
        $this->logs = new Logs( $sectionName.date('Ymd'));
        $this->logs->write("START", "===");
    }

    public function getAllData(){
        return $this->repo->findData();
    }

    public function getData(?int $id = null){
        return $this->repo->findDataId($id);
    }

    public function getDataDetail(MstDistributor $distributor){
        
        return $this->repo->getWithMerchants($distributor);
    }

    public function storeData(array $data)
    {
        try {
            $result = DB::transaction(function() use ($data){
                return $this->repo->storeData($data);
            });

            $this->logs->write("Master Distributor created", "'distributor_id' => {$result->id} || 'code' => {$result->md_code}");

            return $result;

        } catch (\Throwable $e) {
            $this->logs->write("Failed to create Master Distributor", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function updateData(MstDistributor $distributor, array $data)
    {
        try {
            $result = DB::transaction(function() use ($distributor, $data){
                return $this->repo->updateData($distributor, $data);
            });

            $this->logs->write("Master Distributor updated", "'distributor_id' => {$result->id} || 'code' => {$result->md_code}");

            return $result;
        }catch (\Throwable $e) {
            $this->logs->write("Failed to update Master Distributor", "'data' => " . json_encode($data) . " || 'error' => " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteData(MstDistributor $distributor)
    {
        try {
            $distributorId = $distributor->id;
            $distributorCode = $distributor->md_code;

            DB::transaction(function () use ($distributor) {
                $this->repo->deleteData($distributor);
            });

            $this->logs->write(
                "Master Distributor deleted",
                "'distributor_id' => {$distributorId} || 'code' => {$distributorCode}"
            );

            return true;
        }catch (\Throwable $e) {
            $this->logs->write("Failed to delete Master Distributor", "'error' => " . $e->getMessage());
            throw $e;
        }
    }
}
