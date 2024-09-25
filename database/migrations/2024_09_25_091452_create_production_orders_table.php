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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SaleOrderItem::class)->constrained();
            $table->foreignIdFor(\App\Models\Line::class)->constrained();
            $table->foreignIdFor(\App\Models\Factory::class)->constrained();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
