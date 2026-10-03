<?php

namespace App\Services;

use App\Repositories\TranRewardRepository;
use App\Logs;
use App\Models\TransRewardModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class TranRewardService
{
    protected Logs $logs;

    public function __construct(
        protected TranRewardRepository $repo
    ) {
        $this->repo = $repo;

        $this->logs = new Logs(
            "Transaction_" . date('Ymd')
        );

        $this->logs->write("START", "===");
    }

    public function getData()
    {
        return $this->repo->getData();
    }

    public function getDataById(int $id)
    {
        return $this->repo->getDataById($id);
    }

    public function getMyTransactions(int $userId)
    {
        return $this->repo->getByUserId($userId);
    }

    public function storeData(array $data): TransRewardModel
    {
        try {
            $result = DB::transaction(function () use ($data) {

                $data['trc_trx_no'] = $this->genTrxNo();

                return $this->repo->storeData($data);
            });

            $this->logs->write(
                "Transaction created",
                "'transaction_id' => {$result->id} || "
                . "'transaction_number' => {$result->trc_trx_no}"
            );

            return $result;

        } catch (Throwable $e) {

            $this->logs->write(
                "Failed to create Transaction",
                "'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function updateData(
        TransRewardModel $transRewardModel,
        array $data
    ): TransRewardModel {
        try {

            $result = DB::transaction(function () use (
                $transRewardModel,
                $data
            ) {

                $this->repo->updateData(
                    $transRewardModel,
                    $data
                );

                return $transRewardModel->fresh();
            });

            $this->logs->write(
                "Transaction updated",
                "'transaction_id' => {$result->id} || "
                . "'transaction_number' => {$result->trc_trx_no}"
            );

            return $result;

        } catch (Throwable $e) {

            $this->logs->write(
                "Failed to update Transaction",
                "'transaction_id' => {$transRewardModel->id} || "
                . "'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function deleteData(
        TransRewardModel $transRewardModel
    ): bool {
        try {

            // Simpan informasi sebelum delete
            $id = $transRewardModel->id;
            $transactionNumber = $transRewardModel->trc_trx_no;
            $result = DB::transaction(function () use (
                $transRewardModel
            ) {
                return $this->repo->deleteData(
                    $transRewardModel
                );
            });

            $this->logs->write(
                "Transaction deleted",
                "'transaction_id' => {$id} || "
                . "'transaction_number' => {$transactionNumber}"
            );

            return $result;

        } catch (Throwable $e) {

            $this->logs->write(
                "Failed to delete Transaction",
                "'transaction_id' => {$transRewardModel->id} || "
                . "'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    private function genTrxNo(): string
    {
        do {

            $number =
                'TRX-'
                . now()->format('YmdHis')
                . '-'
                . strtoupper(Str::random(5));

        } while (
            $this->repo->existsByTrxNo($number)
        );

        return $number;
    }
}
