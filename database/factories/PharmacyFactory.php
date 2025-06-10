<?php

namespace Database\Factories;

use App\Models\Pharmacy;
use App\Models\User;
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
            'pharmacy_name' => fake()->company(),
            'location' => fake()->city(),
            'owner_id' => 1
        ];
    }

    // public function configure(): Factory
    // {
    //     return $this->afterCreating(function (Pharmacy $pharmacy) {
    //         if (!isset($pharmacy->owner_id)) {
    //             $user = User::factory()->create([
    //                 'pharmacy_id' => $pharmacy->id
    //             ]);

    //             $pharmacy->owner_id = $user->id;
    //             $pharmacy->save();
    //         }
    //     });
    // }
}
