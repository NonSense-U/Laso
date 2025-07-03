<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FastSellingItem>
 */
class FastSellingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement(['cloves','needle','face mask','tissues']),
            'price' => rand(100,1000),
            'base_price' => rand(50,99),
            'quantity' => rand(20,50)
        ];
    }
}
