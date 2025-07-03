<?php

namespace Database\Seeders;

use App\Models\FastSellingItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FastSellingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FastSellingItem::factory(2)->create([
            'pharmacy_id' => 1
        ]);
    }
}
