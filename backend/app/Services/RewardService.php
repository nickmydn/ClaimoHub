<?php

namespace App\Services;

use App\Models\MstReward;
use App\Repositories\RewardRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Logs;

class RewardService
{
    protected Logs $logs;
    public function __construct(protected RewardRepository $repo) {
        $sectionName = "Reward_";
        $this->logs = new Logs( $sectionName.date('Ymd'));
        $this->logs->write("START", "===");
    }

    public function getAll(): Collection
    {
        try {
            return $this->repo->findData();
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Reward",
                "'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function getById(int $id): ?MstReward
    {
        try {
            return $this->repo->findDataId($id);
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Reward by ID",
                "'reward_id' => {$id} || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function getDetail(MstReward $mstReward): MstReward
    {
        try {
            return $this->repo->getDetail($mstReward);
        } catch (\Throwable $e) {
            $this->logs->write(
                "Failed to get Master Reward detail",
                "'reward_id' => {$mstReward->id} || 'error' => " .
                $e->getMessage()
            );

            throw $e;
        }
    }

    public function create(array $data): MstReward
    {
        try {
            $result = DB::transaction(function () use ($data) {

                if ($this->repo->isCodeExists($data['mr_code'])) {
                    throw new Exception(
                        'Reward code already exists.'
                    );
                }

                return $this->repo->storeData($data);
            });

            $this->logs->write(
                "Master Reward created",
                "'reward_id' => {$result->id} || 'code' => {$result->mr_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to create Master Reward",
                "'data' => " . json_encode($data) .
                " || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function update(
        MstReward $mstReward,
        array $data
    ): MstReward {
        try {
            $result = DB::transaction(function () use (
                $mstReward,
                $data
            ) {

                if (
                    $this->repo->isCodeExists(
                        $data['mr_code'],
                        $mstReward->id
                    )
                ) {
                    throw new Exception(
                        'Reward code already exists.'
                    );
                }

                return $this->repo->updateData(
                    $mstReward,
                    $data
                );
            });

            $this->logs->write(
                "Master Reward updated",
                "'reward_id' => {$result->id} || 'code' => {$result->mr_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to update Master Reward",
                "'reward_id' => {$mstReward->id} || 'data' => " .
                json_encode($data) .
                " || 'error' => " . $e->getMessage()
            );

            throw $e;
        }
    }

    public function delete(MstReward $mstReward): bool
    {
        try {
            $result = DB::transaction(function () use ($mstReward) {
                return $this->repo->deleteData($mstReward);
            });

            $this->logs->write(
                "Master Reward deleted",
                "'reward_id' => {$mstReward->id} || 'code' => {$mstReward->mr_code}"
            );

            return $result;

        } catch (\Throwable $e) {

            $this->logs->write(
                "Failed to delete Master Reward",
                "'reward_id' => {$mstReward->id} || 'code' => {$mstReward->mr_code} || 'error' => " .
                $e->getMessage()
            );

            throw $e;
        }
    }
}
