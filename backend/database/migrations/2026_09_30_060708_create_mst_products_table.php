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
        Schema::create('mst_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mp_merchant_id')
                ->constrained('mst_merchant')
                ->cascadeOnDelete();

            $table->string('mp_code', 30);
            $table->string('mp_name', 150);
            $table->decimal('mp_price', 15, 2);
            $table->boolean('is_active')->default(true)->index();

            $table->timestamps();

            $table->unique(['mp_merchant_id', 'mp_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_product');
    }
};
