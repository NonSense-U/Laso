<?php

use App\Models\Medication;
use App\Models\SideEffect;
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
        Schema::create('side_effects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('medications_side_effects', function (Blueprint $table) {
            $table->foreignIdFor(Medication::class)->primary()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SideEffect::class)->primary()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('side_effects');
        Schema::dropIfExists('medications_side_effects');
    }
};
