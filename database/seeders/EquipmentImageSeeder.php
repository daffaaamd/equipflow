<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentImage;
use Illuminate\Database\Seeder;

class EquipmentImageSeeder extends Seeder
{
    public function run(): void
    {
        // All images are locally downloaded verified heavy equipment photos from Wikimedia Commons
        // Format: /img/gallery/{category-slug}_{index}.jpg
        $categoryPools = [
            'hydraulic-excavator' => [
                '/img/gallery/hydraulic-excavator_0.jpg',
                '/img/gallery/hydraulic-excavator_1.jpg',
                '/img/gallery/hydraulic-excavator_2.jpg',
                '/img/gallery/hydraulic-excavator_3.jpg',
            ],
            'mini-excavator' => [
                '/img/gallery/mini-excavator_0.jpg',
                '/img/gallery/mini-excavator_1.jpg',
                '/img/gallery/mini-excavator_2.jpg',
            ],
            'bulldozer' => [
                '/img/gallery/bulldozer_0.jpg',
                '/img/gallery/bulldozer_1.jpg',
                '/img/gallery/bulldozer_2.jpg',
                '/img/gallery/bulldozer_3.jpg',
            ],
            'wheel-loader' => [
                '/img/gallery/wheel-loader_0.jpg',
                '/img/gallery/wheel-loader_1.jpg',
                '/img/gallery/wheel-loader_2.jpg',
            ],
            'skid-steer-loader' => [
                '/img/gallery/skid-steer-loader_0.jpg',
                '/img/gallery/skid-steer-loader_1.jpg',
            ],
            'dump-truck' => [
                '/img/gallery/dump-truck_0.jpg',
                '/img/gallery/dump-truck_1.jpg',
                '/img/gallery/dump-truck_2.jpg',
            ],
            'articulated-dump-truck' => [
                '/img/gallery/articulated-dump-truck_0.jpg',
                '/img/gallery/articulated-dump-truck_1.jpg',
                '/img/gallery/articulated-dump-truck_2.jpg',
            ],
            'crawler-crane' => [
                '/img/gallery/crawler-crane_0.jpg',
                '/img/gallery/crawler-crane_1.jpg',
            ],
            'mobile-crane' => [
                '/img/gallery/mobile-crane_0.jpg',
                '/img/gallery/mobile-crane_1.jpg',
            ],
            'tower-crane' => [
                '/img/gallery/tower-crane_0.jpg',
                '/img/gallery/tower-crane_1.jpg',
            ],
            'truck-mounted-crane' => [
                '/img/gallery/truck-mounted-crane_0.jpg',
                '/img/gallery/truck-mounted-crane_1.jpg',
            ],
            'forklift' => [
                '/img/gallery/forklift_0.jpg',
                '/img/gallery/forklift_1.jpg',
            ],
            'telehandler' => [
                '/img/gallery/telehandler_0.jpg',
                '/img/gallery/telehandler_1.jpg',
            ],
            'reach-stacker' => [
                '/img/gallery/reach-stacker_0.jpg',
                '/img/gallery/reach-stacker_1.jpg',
            ],
            'motor-grader' => [
                '/img/gallery/motor-grader_0.jpg',
                '/img/gallery/motor-grader_1.jpg',
            ],
            'vibro-roller' => [
                '/img/gallery/vibro-roller_0.jpg',
                '/img/gallery/vibro-roller_1.jpg',
            ],
            'road-roller' => [
                '/img/gallery/road-roller_0.jpg',
                '/img/gallery/road-roller_1.jpg',
            ],
            'asphalt-paver' => [
                '/img/gallery/asphalt-paver_0.jpg',
                '/img/gallery/asphalt-paver_1.jpg',
            ],
            'soil-compactor' => [
                '/img/gallery/soil-compactor_0.jpg',
                '/img/gallery/soil-compactor_1.jpg',
            ],
            'rock-breaker' => [
                '/img/gallery/rock-breaker_0.jpg',
                '/img/gallery/rock-breaker_1.jpg',
            ],
            'backhoe-loader' => [
                '/img/gallery/backhoe-loader_0.jpg',
                '/img/gallery/backhoe-loader_1.jpg',
            ],
            'water-tanker' => [
                '/img/gallery/water-tanker_0.jpg',
                '/img/gallery/water-tanker_1.jpg',
            ],
            'fuel-tanker' => [
                '/img/gallery/fuel-tanker_0.jpg',
                '/img/gallery/fuel-tanker_1.jpg',
            ],
            'lowbed-trailer' => [
                '/img/gallery/lowbed-trailer_0.jpg',
                '/img/gallery/lowbed-trailer_1.jpg',
            ],
            'pile-driver' => [
                '/img/gallery/pile-driver_0.jpg',
                '/img/gallery/pile-driver_1.jpg',
            ],
            'concrete-pump' => [
                '/img/gallery/concrete-pump_0.jpg',
                '/img/gallery/concrete-pump_1.jpg',
            ],
        ];

        // Fallback pool uses excavator images (most common equipment)
        $defaultPool = [
            '/img/gallery/hydraulic-excavator_0.jpg',
            '/img/gallery/hydraulic-excavator_1.jpg',
            '/img/gallery/hydraulic-excavator_2.jpg',
        ];

        $captions = [
            'Front quarter perspective',
            'Full unit profile',
            'Operator cabin & controls',
            'Undercarriage & tracks inspection',
            'Hydraulic boom & attachment',
            'Engine compartment inspection',
            'Site mobilization readiness',
        ];

        EquipmentImage::truncate();

        foreach (Equipment::with('category')->get() as $equipment) {
            $slug = $equipment->category?->slug ?? 'hydraulic-excavator';
            $pool = $categoryPools[$slug] ?? $defaultPool;

            // Pick primary image based on equipment ID hash to distribute visuals across fleet
            $primaryIndex = abs(crc32($equipment->equipment_code)) % count($pool);
            $primaryUrl = $pool[$primaryIndex];

            // Primary Image
            EquipmentImage::create([
                'equipment_id' => $equipment->id,
                'image_url' => $primaryUrl,
                'caption' => "{$equipment->name} - Primary Unit View",
                'is_primary' => true,
                'sort_order' => 0,
            ]);

            // 2 Secondary gallery images from the matching category pool
            $sort = 1;
            foreach ($pool as $idx => $secondaryUrl) {
                if ($secondaryUrl === $primaryUrl) {
                    continue;
                }
                if ($sort > 2) {
                    break;
                }

                EquipmentImage::create([
                    'equipment_id' => $equipment->id,
                    'image_url' => $secondaryUrl,
                    'caption' => "{$equipment->name} - " . ($captions[$sort] ?? 'Site Inspection'),
                    'is_primary' => false,
                    'sort_order' => $sort,
                ]);
                $sort++;
            }
        }
    }
}