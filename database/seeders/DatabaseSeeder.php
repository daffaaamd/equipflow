<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            EquipmentCategorySeeder::class,
            CustomerSeeder::class,
            EquipmentSeeder::class,
            EquipmentImageSeeder::class,
            ProjectSeeder::class,
            OperatorSeeder::class,
            RentalFlowSeeder::class,
        ]);
    }
}
