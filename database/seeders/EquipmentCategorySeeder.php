<?php

namespace Database\Seeders;

use App\Models\EquipmentCategory;
use Illuminate\Database\Seeder;

class EquipmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        // All images are locally downloaded verified heavy equipment photos from Wikimedia Commons
        $categories = [
            ['name' => 'Hydraulic Excavator', 'slug' => 'hydraulic-excavator', 'weight' => 30, 'code' => 'EX', 'image' => '/img/categories/hydraulic-excavator.jpg', 'description' => 'Tracked hydraulic excavators from 7 to 45 tons for general excavation and earthmoving.'],
            ['name' => 'Mini Excavator', 'slug' => 'mini-excavator', 'weight' => 6, 'code' => 'MX', 'image' => '/img/categories/mini-excavator.jpg', 'description' => 'Compact excavators up to 6 tons for tight urban and utility work.'],
            ['name' => 'Bulldozer', 'slug' => 'bulldozer', 'weight' => 12, 'code' => 'BD', 'image' => '/img/categories/bulldozer.jpg', 'description' => 'Crawler dozers from D65 to D375 class for site clearing and grading.'],
            ['name' => 'Wheel Loader', 'slug' => 'wheel-loader', 'weight' => 10, 'code' => 'WL', 'image' => '/img/categories/wheel-loader.jpg', 'description' => 'Articulated wheel loaders from 1.5 to 8 cubic meter buckets.'],
            ['name' => 'Skid Steer Loader', 'slug' => 'skid-steer-loader', 'weight' => 4, 'code' => 'SS', 'image' => '/img/categories/skid-steer-loader.jpg', 'description' => 'Compact skid steer loaders for material handling in confined spaces.'],
            ['name' => 'Dump Truck', 'slug' => 'dump-truck', 'weight' => 18, 'code' => 'DT', 'image' => '/img/categories/dump-truck.jpg', 'description' => 'Rear dump trucks from 10 to 60 tons for hauling overburden and material.'],
            ['name' => 'Articulated Dump Truck', 'slug' => 'articulated-dump-truck', 'weight' => 7, 'code' => 'AD', 'image' => '/img/categories/articulated-dump-truck.jpg', 'description' => 'ADTs from 25 to 45 tons for all-terrain hauling.'],
            ['name' => 'Crawler Crane', 'slug' => 'crawler-crane', 'weight' => 5, 'code' => 'CC', 'image' => '/img/categories/crawler-crane.jpg', 'description' => 'Heavy lift crawler cranes from 50 to 300 tons.'],
            ['name' => 'Mobile Crane', 'slug' => 'mobile-crane', 'weight' => 6, 'code' => 'MC', 'image' => '/img/categories/mobile-crane.jpg', 'description' => 'Rough terrain and all-terrain cranes from 25 to 120 tons.'],
            ['name' => 'Tower Crane', 'slug' => 'tower-crane', 'weight' => 4, 'code' => 'TC', 'image' => '/img/categories/tower-crane.jpg', 'description' => 'Topkit and luffing tower cranes for high-rise construction.'],
            ['name' => 'Truck-Mounted Crane', 'slug' => 'truck-mounted-crane', 'weight' => 4, 'code' => 'TM', 'image' => '/img/categories/truck-mounted-crane.jpg', 'description' => 'Carrier-mounted cranes for rapid mobilization and lifting.'],
            ['name' => 'Forklift', 'slug' => 'forklift', 'weight' => 8, 'code' => 'FK', 'image' => '/img/categories/forklift.jpg', 'description' => 'Counterbalance forklifts from 2.5 to 10 tons for warehousing and logistics.'],
            ['name' => 'Telehandler', 'slug' => 'telehandler', 'weight' => 5, 'code' => 'TH', 'image' => '/img/categories/telehandler.jpg', 'description' => 'Reach forklifts up to 17 meters for elevated material placement.'],
            ['name' => 'Reach Stacker', 'slug' => 'reach-stacker', 'weight' => 3, 'code' => 'RS', 'image' => '/img/categories/reach-stacker.jpg', 'description' => 'Reach stackers for container and heavy cargo handling.'],
            ['name' => 'Motor Grader', 'slug' => 'motor-grader', 'weight' => 7, 'code' => 'MG', 'image' => '/img/categories/motor-grader.jpg', 'description' => 'Motor graders from 120 to 260 HP for road and site leveling.'],
            ['name' => 'Vibro Roller', 'slug' => 'vibro-roller', 'weight' => 5, 'code' => 'VR', 'image' => '/img/categories/vibro-roller.jpg', 'description' => 'Self-propelled vibratory rollers for soil and asphalt compaction.'],
            ['name' => 'Road Roller', 'slug' => 'road-roller', 'weight' => 3, 'code' => 'RR', 'image' => '/img/categories/road-roller.jpg', 'description' => 'Tandem and pneumatic road rollers for finishing compaction.'],
            ['name' => 'Asphalt Paver', 'slug' => 'asphalt-paver', 'weight' => 3, 'code' => 'AP', 'image' => '/img/categories/asphalt-paver.jpg', 'description' => 'Tracked and wheeled pavers for asphalt and concrete paving.'],
            ['name' => 'Soil Compactor', 'slug' => 'soil-compactor', 'weight' => 4, 'code' => 'CP', 'image' => '/img/categories/soil-compactor.jpg', 'description' => 'Compactors for embankment and trench compaction.'],
            ['name' => 'Hydraulic Rock Breaker', 'slug' => 'rock-breaker', 'weight' => 4, 'code' => 'RB', 'image' => '/img/categories/rock-breaker.jpg', 'description' => 'Excavator-mounted rock breakers for quarry and demolition.'],
            ['name' => 'Backhoe Loader', 'slug' => 'backhoe-loader', 'weight' => 8, 'code' => 'BH', 'image' => '/img/categories/backhoe-loader.jpg', 'description' => 'Versatile backhoe loaders for utilities and small earthworks.'],
            ['name' => 'Water Tanker', 'slug' => 'water-tanker', 'weight' => 4, 'code' => 'WT', 'image' => '/img/categories/water-tanker.jpg', 'description' => 'Water trucks for dust suppression and site watering.'],
            ['name' => 'Fuel Tanker', 'slug' => 'fuel-tanker', 'weight' => 3, 'code' => 'FT', 'image' => '/img/categories/fuel-tanker.jpg', 'description' => 'Mobile refueling tankers for remote fleet operations.'],
            ['name' => 'Lowbed Trailer', 'slug' => 'lowbed-trailer', 'weight' => 4, 'code' => 'LB', 'image' => '/img/categories/lowbed-trailer.jpg', 'description' => 'Lowbed and flatbed trailers for heavy equipment transport.'],
            ['name' => 'Pile Driver', 'slug' => 'pile-driver', 'weight' => 3, 'code' => 'PD', 'image' => '/img/categories/pile-driver.jpg', 'description' => 'Vibratory and hydraulic pile drivers for foundation works.'],
            ['name' => 'Concrete Pump', 'slug' => 'concrete-pump', 'weight' => 3, 'code' => 'PC', 'image' => '/img/categories/concrete-pump.jpg', 'description' => 'Boom and line concrete pumps for high-volume placement.'],
        ];

        foreach ($categories as $i => $cat) {
            EquipmentCategory::updateOrCreate(
                ['slug' => $cat['slug']],
                [
                    'name' => $cat['name'],
                    'description' => $cat['description'],
                    'image_url' => $cat['image'],
                    'sort_order' => $i + 1,
                ]
            );
        }
    }
}