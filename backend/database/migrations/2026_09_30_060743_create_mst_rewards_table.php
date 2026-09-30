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
        Schema::create('mst_reward', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mr_voucher_product_id')
                ->constrained('mst_voucher_product')
                ->restrictOnDelete();

            $table->string('mr_code', 50)->unique();
            $table->string('mr_name', 150);

            $table->decimal('mr_min_transaction', 15, 2);

            $table->integer('mr_quota');

            $table->date('mr_start_date');
            $table->date('mr_end_date');

            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_reward');
    }
};
