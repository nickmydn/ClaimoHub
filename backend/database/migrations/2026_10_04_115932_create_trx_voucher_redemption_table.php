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
        Schema::create('trx_voucher_redemption', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tvr_reward_order_trans_id')
                ->unique()
                ->constrained('reward_order_trans')
                ->restrictOnDelete();

            $table->foreignId('tvr_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('tvr_voucher_product_id')
                ->constrained('mst_voucher_product')
                ->restrictOnDelete();

            $table->string('tvr_redemption_no', 50)
                ->unique();

            $table->string('tvr_external_reference', 100)
                ->nullable()
                ->unique();

            $table->string('tvr_voucher_code', 100)
                ->nullable();

            $table->decimal('tvr_amount', 15, 2);

            $table->string('tvr_status', 30)
                ->default('pending')
                ->index();

            $table->text('tvr_failure_reason')
                ->nullable();

            $table->timestamp('tvr_redeemed_at')
                ->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trx_voucher_redemption');
    }
};
