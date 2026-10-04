<?php

namespace Tests\Feature;

use App\Models\MstDistributor;
use App\Models\MstMerchant;
use App\Models\MstReward;
use App\Models\MstVoucherProduct;
use App\Models\TransRewardModel;
use App\Models\User;
use App\Services\RewardEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RewardEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected MstMerchant $merchant;
    protected MstVoucherProduct $voucherProduct;
    protected MstReward $reward;

    protected function setUp(): void
    {
        parent::setUp();

        /*
        |--------------------------------------------------------------------------
        | User
        |--------------------------------------------------------------------------
        */

        $this->user = User::factory()->create([
            'role' => 'customer',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Distributor
        |--------------------------------------------------------------------------
        */

        $distributor = MstDistributor::create([
            'md_code' => 'DST-TEST-001',
            'md_name' => 'Distributor Test',
            'md_email' => 'distributor@test.com',
            'md_phone' => '08123456789',
            'md_address' => 'Jakarta',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Merchant
        |--------------------------------------------------------------------------
        */

        $this->merchant = MstMerchant::create([
            'mm_distributor_id' => $distributor->id,
            'mm_code' => 'MRC-TEST-001',
            'mm_name' => 'Merchant Test',
            'mm_email' => 'merchant@test.com',
            'mm_phone' => '08123456789',
            'mm_address' => 'Jakarta',
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Voucher Product
        |--------------------------------------------------------------------------
        */

        $this->voucherProduct = MstVoucherProduct::create([
            'mvp_provider' => 'Mock Provider',
            'mvp_code' => 'VCR-TEST-001',
            'mvp_name' => 'Voucher Test Rp50.000',
            'mvp_denomination' => 50000,
            'mvp_stock' => 10,
            'is_active' => true,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Reward
        |--------------------------------------------------------------------------
        */

        $this->reward = MstReward::create([
            'mr_voucher_product_id' => $this->voucherProduct->id,
            'mr_code' => 'RWD-TEST-001',
            'mr_name' => 'Reward Test',
            'mr_min_transaction' => 100000,
            'mr_quota' => 5,
            'mr_start_date' => now()->startOfDay(),
            'mr_end_date' => now()->addDays(30)->startOfDay(),
            'is_active' => true,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Test 1
    |--------------------------------------------------------------------------
    */

    public function test_eligible_transaction_creates_reward(): void
    {
        $transaction = TransRewardModel::create([
            'trc_user_id' => $this->user->id,
            'trc_merchant_id' => $this->merchant->id,
            'trc_trx_no' => 'TRX-TEST-001',
            'trc_amount' => 150000,
            'trc_status' => 'completed',
            'trc_trx_date' => now(),
        ]);

        $service = app(RewardEligibilityService::class);

        $rewardOrder = $service->checkAndCreateReward(
            $transaction
        );

        $this->assertNotNull($rewardOrder);

        $this->assertDatabaseHas(
            'reward_order_trans',
            [
                'transaction_id' => $transaction->id,
                'campaign_id' => $this->reward->id,
                'voucher_product_id' => $this->voucherProduct->id,
                'reward_amount' => 50000,
                'status' => 'pending',
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Quota berkurang
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas(
            'mst_reward',
            [
                'id' => $this->reward->id,
                'mr_quota' => 4,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Stock berkurang
        |--------------------------------------------------------------------------
        */

        $this->assertDatabaseHas(
            'mst_voucher_product',
            [
                'id' => $this->voucherProduct->id,
                'mvp_stock' => 9,
            ]
        );
    }
}
