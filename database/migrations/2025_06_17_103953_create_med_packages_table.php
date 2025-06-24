<?php

use App\Models\Medication;
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
        Schema::create('med_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Medication::class)->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamp('production_date');
            $table->timestamp('expiration_date');
            $table->foreignIdFor(Supplier::class);
            $table->foreignIdFor(Pharmacy::class);
            $table->integer('purchase_price');
            $table->boolean('is_viable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_packages');
    }
};
