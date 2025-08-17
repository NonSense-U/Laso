<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pharmacy_id' => 1,
            'admitted_by' => 1,
            'full_name' => fake()->firstName() . fake()->lastName(),
            'phone_number' => fake()->phoneNumber()
        ];
    }
}
