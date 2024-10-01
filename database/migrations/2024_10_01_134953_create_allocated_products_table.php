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
        Schema::create('allocated_products', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Product::class)->constrained();
            $table->foreignIdFor(\App\Models\SaleOrder::class)->constrained();
            $table->integer('allocated_quantity');
            $table->enum('status',[
                'Allocated','Used'
            ])->default('Allocated');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocated_products');
    }
};
