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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignIdFor(\App\Models\Supplier::class)->constrained();
            $table->dateTime('order_date');
            $table->enum('status', ['Approved', 'Rejected', 'Pending'])->default('pending');
            $table->decimal('total_amount', 15, 0)->default(0);
            $table->decimal('total_price', 15, 0)->default(0);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
