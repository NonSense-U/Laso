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
        Schema::create('pharmacy_staff_whitelist', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pharmacy::class)->index()->constrained()->cascadeOnDelete();
            $table->string('email')->unique(); //! DELETE WHEN WORKER IS DISABLED
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pharmacy_staff_whitelist');
    }
};
