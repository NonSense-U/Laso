<?php

namespace Database\Seeders;

use App\Models\Insurance;
use App\Models\Patient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Patient::create(
            [
                'pharmacy_id' => 1,
                'admitted_by' => 1,
                'full_name' => "Mario Italiano",
                'phone_number' => "0992522375"
            ]
        );

        Insurance::create([
            'pharmacy_id' => 1,
            'user_id' => 1,
            'patient_id' => 1,
            'provider' => 'Kamila Haris',
            'discount_percentage' => 50
        ]);

        Patient::factory(4)->create();
    }
}
