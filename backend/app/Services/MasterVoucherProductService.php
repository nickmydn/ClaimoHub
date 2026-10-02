<?php

namespace App\Services;

use App\Models\MstVoucherProduct;
use App\Repositories\MasterVoucherProductRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Logs;

class MasterVoucherProductService
{
    protected Logs $logs;
    public function __construct(protected MasterVoucherProductRepository $repo) {
        $sectionName = "Master_Voucher_Product_";
        $this->logs = new Logs( $sectionName.date('Ymd'));
        $this->logs->write("START", "===");
    }

    public function getAll(): Collection
    {
        try {
            return $this->repo->findData();
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Voucher Product",
                "'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function getById(int $id): ?MstVoucherProduct
    {
        try {
            return $this->repo->findDataId($id);
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Voucher Product by ID",
                "'voucher_product_id' => {$id} || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function getDetail(
        MstVoucherProduct $mstVoucherProduct
    ): MstVoucherProduct {
        try {
            return $this->repo->getDetail($mstVoucherProduct);
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Voucher Product detail",
                "'voucher_product_id' => {$mstVoucherProduct->id} || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function create(array $data): MstVoucherProduct
    {
        try {
            $result = DB::transaction(function () use ($data) {

                if ($this->repo->isCodeExists($data['mvp_code'])) {
                    throw new Exception(
                        'Voucher product code already exists.'
                    );
                }

                return $this->repo->storeData($data);
            });

            $this->logs->write(
                "Master Voucher Product created",
                "'voucher_product_id' => {$result->id} || 'code' => {$result->mvp_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to create Master Voucher Product",
                "'data' => " . json_encode($data) .
                " || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function update(
        MstVoucherProduct $mstVoucherProduct,
        array $data
    ): MstVoucherProduct {
        try {
            $result = DB::transaction(function () use (
                $mstVoucherProduct,
                $data
            ) {

                if (
                    $this->repo->isCodeExists(
                        $data['mvp_code'],
                        $mstVoucherProduct->id
                    )
                ) {
                    throw new Exception(
                        'Voucher product code already exists.'
                    );
                }

                return $this->repo->updateData(
                    $mstVoucherProduct,
                    $data
                );
            });

            $this->logs->write(
                "Master Voucher Product updated",
                "'voucher_product_id' => {$result->id} || 'code' => {$result->mvp_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to update Master Voucher Product",
                "'voucher_product_id' => {$mstVoucherProduct->id} || 'data' => " .
                json_encode($data) .
                " || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function delete(
        MstVoucherProduct $mstVoucherProduct
    ): bool {
        try {
            $result = DB::transaction(function () use ($mstVoucherProduct) {

                if ($mstVoucherProduct->mst_rewards()->exists()) {
                    throw new Exception(
                        'Voucher product cannot be deleted because it has reward data.'
                    );
                }

                return $this->repo->deleteData($mstVoucherProduct);
            });

            $this->logs->write(
                "Master Voucher Product deleted",
                "'voucher_product_id' => {$mstVoucherProduct->id} || 'code' => {$mstVoucherProduct->mvp_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to delete Master Voucher Product",
                "'voucher_product_id' => {$mstVoucherProduct->id} || 'code' => {$mstVoucherProduct->mvp_code} || 'error' => " .
                $e->getMessage()
            );

            throw $e;
        }
    }
}
