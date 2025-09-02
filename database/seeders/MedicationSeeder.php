<?php

namespace Database\Seeders;

use App\Helpers\medicationPriceHelper;
use App\Models\Manufacturer;
use App\Models\Medication;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Manufacturer::factory()->create();
        medicationPriceHelper::getPrices();
        // Medication::factory(5)->create();
        Medication::factory()->create([
            'name' => 'Osteofix',
            'scientific_name' => 'Osteofix',
            'serial_number' => 6215857101877
        ]);
    }
}
