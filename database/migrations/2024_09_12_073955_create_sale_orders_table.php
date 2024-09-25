<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->string("code", 55)->unique();
            $table->date('order_date');
            $table->foreignIdFor(\App\Models\Customer::class)->constrained();
            $table->integer('total_price')->default(0);
            $table->integer('total_amount')->default(0);
            $table->enum('status', [
                'Pending',
                'Pending Production',
                'In Production',
                'Completed',
                'Cancelled',
                'On Hold'
            ])->default('Pending');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_orders');
    }
};
