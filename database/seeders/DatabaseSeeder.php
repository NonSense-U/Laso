<?php

namespace Database\Seeders;

use App\Models\Pharmacy;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Pest\Laravel\call;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $super = User::factory()->create([
            'username' => 'SUPER',
            'email' => 'SUPER@example.com',
        ]);
        $global_pharmacy = Pharmacy::factory()->create();
        $super->pharmacy_id = $global_pharmacy->id;
        $super->save();

        $this->call([
            PharmacySeeder::class,
            UserSeeder::class
        ]);
    }
}
