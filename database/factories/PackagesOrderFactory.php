<?php

namespace Database\Factories;

use App\Models\Pharmacy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PackagesOrder>
 */
class PackagesOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => rand(1, 5),
            'pharmacy_id' => '1',
            'total_price' => rand(100,1000),
            'paid_amount' => rand(100,1000)
        ];
    }
}
