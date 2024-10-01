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
        Schema::create('sale_order_items', function (Blueprint $table) {
            $table->id();
            $table->string('code',55)->unique();
            $table->foreignIdFor(\App\Models\SaleOrder::class)->constrained();
            $table->foreignIdFor(\App\Models\Product::class)->constrained();
            $table->integer('unit_price');
            $table->integer('quantity')->default(0);
            $table->integer('total_price')->default(0);
            $table->date("delivery_date")->nullable();
            $table->string('description')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_order_items');
    }
};
