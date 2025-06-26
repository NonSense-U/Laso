<?php

namespace Database\Factories;

use App\Models\Medication;
use App\Models\PackagesOrder;
use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedPackage>
 */
class MedPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medication_id' => rand(1,5),
            'quantity' => rand(1, 100),
            'production_date' => fake()->date(),
            'expiration_date' => fake()->date(),
            'pharmacy_id' => Pharmacy::factory(),
            'packages_order_id' => PackagesOrder::factory(),
            'purchase_price' => rand(100, 1000),
            'is_viable' => true
        ];
    }
}
