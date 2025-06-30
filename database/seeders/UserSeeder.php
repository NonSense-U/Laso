<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory(5)->hasPharmacy()->create();
        User::factory(10)->isWhiteListed()->create([
            'pharmacy_id' => 1
        ]);
    }
}
