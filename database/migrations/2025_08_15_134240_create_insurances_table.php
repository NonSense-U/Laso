<?php

use App\Models\Patient;
use App\Models\Pharmacy;
use App\Models\User;
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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pharmacy::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Patient::class);
            $table->string('provider');
            $table->integer('discount_percentage');
            $table->timestamps();
        
            //TODO $table->check('discount_percentage')
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
