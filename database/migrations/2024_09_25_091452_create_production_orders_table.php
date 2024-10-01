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
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->foreignIdFor(\App\Models\Line::class)->constrained();
            $table->foreignIdFor(\App\Models\Factory::class)->constrained();
            $table->foreignIdFor(\App\Models\Product::class)->constrained();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', [
                'Pending Production',
                'In Production',
                'Completed',
                'Cancelled',
                'On Hold'
            ])->default('Pending Production');
            $table->integer('quantity')->default(0);
            $table->timestamp('deleted_at')->nullable();
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
