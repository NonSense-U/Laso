<?php

namespace Database\Seeders;

use App\Models\PackagesOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackagesOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PackagesOrder::factory(5)->create();
    }
}
