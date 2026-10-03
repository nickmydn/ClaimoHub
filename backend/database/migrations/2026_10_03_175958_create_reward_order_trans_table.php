<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reward_order_trans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')
                ->unique()
                ->constrained('trx_reward_claim')
                ->restrictOnDelete();

            $table->foreignId('campaign_id')
                ->constrained('mst_reward')
                ->restrictOnDelete();

            $table->foreignId('voucher_product_id')
                ->constrained('mst_voucher_product')
                ->restrictOnDelete();

            $table->decimal(
                'reward_amount',
                15,
                2
            );

            $table->string(
                'status',
                20
            )->default('pending')->index();

            $table->timestamp('awarded_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_order_trans');
    }
};
