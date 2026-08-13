<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class EquipflowImageUpdaterSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            EquipmentCategorySeeder::class,
            EquipmentImageSeeder::class,
        ]);
    }
}
