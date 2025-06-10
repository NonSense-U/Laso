<?php

use App\Models\Indication;
use App\Models\Medication;
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
        Schema::create('indications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('medications_indications', function (Blueprint $table) {
            $table->foreignIdFor(Medication::class)->primary()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Indication::class)->primary()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('indications');
        Schema::dropIfExists('medications_indications');
    }
};
