<?php

use App\Models\Pharmacy;
use App\Models\Supplier;
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
        Schema::create('packages_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pharmacy::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Supplier::class);
            $table->decimal('total_price');
            $table->decimal('paid_ammount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages_orders');
    }
};
