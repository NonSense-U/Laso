<?php

namespace Database\Seeders;

use App\Models\MedPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MedPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MedPackage::factory(100)->create([
            'pharmacy_id' => 1,
        ]);
    }
}
