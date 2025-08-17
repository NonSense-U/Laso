<?php

namespace Database\Seeders;

use App\Helpers\medicationPriceHelper;
use App\Models\Pharmacy;
use App\Models\Supplier;
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
            'username' => 'aki',
            'email' => 'aki@gmail.com',
            'phone_number' => '123'
        ]);


        $global_pharmacy = Pharmacy::factory()->create();
        $super->pharmacy_id = $global_pharmacy->id;
        $super->save();

        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            SupplierSeeder::class,
            PackagesOrderSeeder::class,
            MedicationSeeder::class,
            MedPackageSeeder::class,
            FastSellingItemSeeder::class,
            PatientSeeder::class,
        ]);
        
        $super->assignRole('super_admin');
        medicationPriceHelper::getPrices();
    }
}
