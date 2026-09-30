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
        Schema::create('mst_merchant', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mm_distributor_id')
                ->constrained('mst_distributor')
                ->cascadeOnDelete();
            $table->string('mm_code', 30)->unique();
            $table->string('mm_name', 150);
            $table->string('mm_email', 150)->nullable();
            $table->string('mm_phone', 30)->nullable();
            $table->text('mm_address')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->unique(['mm_distributor_id', 'mm_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_merchant');
    }
};
