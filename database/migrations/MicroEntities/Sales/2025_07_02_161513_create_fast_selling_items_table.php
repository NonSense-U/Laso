<?php

use App\Models\Pharmacy;
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
        Schema::create('fast_selling_items', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pharmacy::class)->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('base_price');
            $table->decimal('price');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fast_selling_items');
    }
};
