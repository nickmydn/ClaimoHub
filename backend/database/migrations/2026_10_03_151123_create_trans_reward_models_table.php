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
        Schema::create('trx_reward_claim', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trc_user_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->foreignId('trc_merchant_id')
                ->constrained('mst_merchant')
                ->restrictOnDelete();

            $table->string('trc_trx_no', 50)
                ->unique();

            $table->decimal('trc_amount', 15, 2);

            $table->string('trc_status', 20)
                ->default('completed')
                ->index();

            $table->timestamp('trc_trx_date')
                ->useCurrent()
                ->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trans_reward_models');
    }
};
