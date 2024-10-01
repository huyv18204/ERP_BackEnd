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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\PurchaseOrder::class)->constrained();
            $table->foreignIdFor(\App\Models\Material::class)->constrained();
            $table->decimal('quantity', 15, 0);
            $table->decimal('unit_price', 15, 0);
            $table->decimal('total_price', 15, 0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
