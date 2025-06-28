<?php

namespace Database\Factories;

use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Medication>
 */
class MedicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->colorName(),
            'serial_number' => rand(100,10000),
            'scientific_name' => 'Paradol',
            'price' =>  100,
            'strength' => '500mg',
            'notes' => 'i am a note',
            'manufacturer_id' => Manufacturer::factory()
        ];
    }
}
