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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Pharmacy::class)->constrained()->cascadeOnDelete();
            $table->enum('type',['salary', 'maintenance', 'daily', 'donation', 'other']);
            $table->decimal('amount_drawn');
            $table->string('note')->nullable();
            $table->timestamp('drawn_at')->nullable()->default(now());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
