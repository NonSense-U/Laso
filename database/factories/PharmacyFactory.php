<?php

namespace Database\Factories;

use App\Models\Pharmacy;
use App\Models\User;
use App\Models\Vault;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pharmacy>
 */
class PharmacyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'location' => fake()->city(),
            'owner_id' => 1
        ];
    }

    public function configure(): Factory
    {
        return $this->afterCreating(function (Pharmacy $pharmacy) {
            Vault::create([
                'pharmacy_id' => $pharmacy->id,
                'name' => 'main',
            ]);

            Vault::create([
                'pharmacy_id' => $pharmacy->id,
                'name' => 'charity',
            ]);

        });
    }
}
