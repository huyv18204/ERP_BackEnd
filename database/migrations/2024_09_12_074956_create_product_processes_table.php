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
        Schema::create('product_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\SaleOrderItem::class)->constrained();
            $table->foreignIdFor(\App\Models\Process::class)->constrained();
            $table->integer('std_workTime')->default(0);
            $table->string("description")->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_processes');
    }
};
