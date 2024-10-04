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
        Schema::create('stock_out_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Material::class)->constrained();
            $table->foreignIdFor(\App\Models\StockOut::class);
            $table->integer('quantity');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_out_items');
    }
};
