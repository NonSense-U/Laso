<?php

use App\Models\MedPackage;
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
        Schema::create('opened_meds', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(MedPackage::class)->constrained()->cascadeOnDelete();
            $table->integer('blister_packs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opened_meds');
    }
};
