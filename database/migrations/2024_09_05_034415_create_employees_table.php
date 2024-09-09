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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string("employee_code",10)->unique();
            $table->string('name',55);
            $table->string('address',255)->nullable();
            $table->string('phone',255)->nullable();
            $table->date('work_date')->nullable();
            $table->foreignIdFor(\App\Models\Department::class)->constrained();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
