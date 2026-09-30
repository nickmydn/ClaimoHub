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
        Schema::create('mst_voucher_product', function (Blueprint $table) {
            $table->id();
            $table->string('mvp_provider', 50);
            $table->string('mvp_code', 50)->unique();
            $table->string('mvp_name', 150);
            $table->decimal('mvp_denomination', 15, 2);
            $table->integer('mvp_stock')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_voucher_product');
    }
};
