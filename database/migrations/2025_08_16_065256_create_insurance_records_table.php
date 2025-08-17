<?php

use App\Models\Cart;
use App\Models\Insurance;
use App\Models\Patient;
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
        Schema::create('insurance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Insurance::class);
            $table->foreignIdFor(Cart::class);
            $table->decimal('discounted_amount');
            $table->enum('status', ['pending', 'paid'])->nullable()->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurance_records');
    }
};
