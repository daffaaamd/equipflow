<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentCategory;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run(): void
    {
        $categories = EquipmentCategory::all()->keyBy('slug');

        $fleet = [
            'hydraulic-excavator' => [
                ['Komatsu', 'PC200-8', 20400, 148, 0.8, 340, 3200000],
                ['Komatsu', 'PC300LC-8', 32000, 257, 1.4, 510, 4800000],
                ['Komatsu', 'PC400LC-8', 42000, 348, 1.9, 630, 6500000],
                ['Caterpillar', '320D2', 20700, 152, 0.8, 345, 3300000],
                ['Caterpillar', '330D2', 29800, 246, 1.3, 480, 4700000],
                ['Caterpillar', '336D2', 33700, 271, 1.6, 560, 5400000],
                ['Hitachi', 'ZX210LC', 21800, 160, 0.8, 380, 3500000],
                ['Hitachi', 'ZX330-5G', 33000, 255, 1.4, 530, 5000000],
                ['Hitachi', 'ZX490LCH', 48000, 348, 2.2, 640, 7200000],
                ['Kobelco', 'SK210LC', 21000, 156, 0.8, 350, 3400000],
                ['Kobelco', 'SK330LC', 33000, 250, 1.5, 500, 4900000],
                ['Volvo', 'EC210D', 20900, 163, 0.85, 300, 3500000],
                ['Volvo', 'EC480D', 47800, 315, 2.2, 700, 7100000],
                ['Sany', 'SY215C', 21900, 169, 0.9, 330, 2900000],
                ['Sany', 'SY335C', 33500, 245, 1.4, 500, 4500000],
            ],
            'mini-excavator' => [
                ['Kubota', 'KX155-5', 5500, 36, 0.25, 80, 1050000],
                ['Kubota', 'KX057-4', 5700, 38, 0.22, 75, 1100000],
                ['Komatsu', 'PC78US-6', 7890, 60, 0.38, 130, 1250000],
                ['Caterpillar', '305.5E2', 5500, 38, 0.2, 60, 950000],
                ['Volvo', 'EC35D', 3600, 24, 0.12, 45, 800000],
                ['Yanmar', 'VIO35-6A', 3500, 25, 0.11, 38, 750000],
            ],
            'bulldozer' => [
                ['Komatsu', 'D65EX-18', 17500, 168, 0, 390, 4800000],
                ['Komatsu', 'D85EX-15', 24800, 244, 0, 440, 6000000],
                ['Komatsu', 'D155AX-6', 40500, 361, 0, 640, 8500000],
                ['Komatsu', 'D375A-5', 63500, 532, 0, 1040, 13500000],
                ['Caterpillar', 'D6T', 18700, 186, 0, 400, 5200000],
                ['Caterpillar', 'D7R', 25600, 259, 0, 500, 6500000],
                ['Caterpillar', 'D8R', 37000, 354, 0, 610, 8800000],
                ['Caterpillar', 'D9R', 49000, 476, 0, 800, 12000000],
                ['Shantui', 'SD16', 17000, 164, 0, 350, 3900000],
                ['Shantui', 'SD32', 35500, 320, 0, 570, 7500000],
            ],
            'wheel-loader' => [
                ['Komatsu', 'WA320-6', 13000, 158, 2.3, 210, 2800000],
                ['Komatsu', 'WA380-6', 17400, 187, 2.8, 280, 3300000],
                ['Komatsu', 'WA470-6', 25300, 260, 3.9, 360, 4200000],
                ['Komatsu', 'WA600-6', 37400, 397, 6.4, 560, 6500000],
                ['Caterpillar', '950H', 19000, 190, 3.0, 350, 3500000],
                ['Caterpillar', '966H', 22800, 237, 3.5, 405, 4000000],
                ['Caterpillar', '980H', 28600, 298, 4.5, 470, 5200000],
                ['Volvo', 'L120F', 18500, 197, 3.0, 270, 3400000],
                ['Volvo', 'L150F', 22500, 255, 3.8, 340, 4200000],
                ['Volvo', 'L220F', 29700, 316, 5.2, 410, 5500000],
            ],
            'skid-steer-loader' => [
                ['Bobcat', 'S175', 3200, 49, 0.5, 90, 1100000],
                ['Bobcat', 'S250', 3500, 60, 0.6, 95, 1250000],
                ['Bobcat', 'T770', 4000, 90, 0.7, 100, 1450000],
                ['Case', 'SR250', 3700, 74, 0.65, 95, 1300000],
            ],
            'dump-truck' => [
                ['Scania', 'P380', 18000, 380, 22, 500, 2200000],
                ['Scania', 'P410', 20000, 410, 24, 520, 2500000],
                ['Hino', 'FM260', 14000, 260, 16, 300, 1700000],
                ['Hino', 'FM320', 16000, 320, 18, 320, 1900000],
                ['Mitsubishi FUSO', 'FN527', 19000, 350, 24, 500, 2200000],
                ['Isuzu', 'Giga FVZ', 16000, 280, 18, 300, 1750000],
                ['Volvo', 'FMX440', 26000, 440, 28, 700, 3200000],
                ['Sinotruk', 'HOWO 371', 19000, 371, 20, 400, 1800000],
                ['Mercedes-Benz', 'Actros 3340', 21000, 400, 22, 580, 2600000],
                ['UD Trucks', 'Quon CWE', 20000, 371, 22, 440, 2400000],
            ],
            'articulated-dump-truck' => [
                ['Caterpillar', '740B', 30000, 426, 32, 420, 4800000],
                ['Volvo', 'A40F', 28000, 402, 30, 400, 4600000],
                ['Volvo', 'A35F', 25000, 375, 28, 380, 4200000],
                ['Bell', 'B40D', 29000, 430, 30, 400, 4400000],
                ['Komatsu', 'HM400-3', 28000, 489, 32, 420, 4700000],
                ['Doosan', 'DA40', 28000, 470, 30, 400, 4500000],
            ],
            'crawler-crane' => [
                ['Sumitomo', 'SCX900A', 28000, 200, 0, 350, 9500000],
                ['Sumitomo', 'SCX1500A', 42000, 310, 0, 450, 14500000],
                ['Kobelco', 'CK1300G', 38000, 280, 0, 420, 13500000],
                ['Liebherr', 'LR1200', 120000, 800, 0, 900, 28000000],
                ['Tadano', 'GTC800', 24000, 180, 0, 320, 8500000],
            ],
            'mobile-crane' => [
                ['Tadano', 'GR-80', 20000, 170, 0, 250, 6000000],
                ['Tadano', 'GR-120', 25000, 220, 0, 300, 7500000],
                ['Tadano', 'ATF130', 42000, 400, 0, 500, 12500000],
                ['Kato', 'NK-500', 22000, 190, 0, 260, 6500000],
                ['Liebherr', 'LTM1050', 24000, 250, 0, 350, 8000000],
                ['Terex', 'RT780', 26000, 270, 0, 380, 8500000],
            ],
            'tower-crane' => [
                ['Potain', 'MC175', 15000, 75, 0, 150, 7000000],
                ['Potain', 'MC235', 22000, 95, 0, 180, 9000000],
                ['Liebherr', '110EC-H', 18000, 88, 0, 160, 8000000],
                ['Liebherr', '130EC-B', 20000, 95, 0, 170, 8500000],
            ],
            'truck-mounted-crane' => [
                ['Tadano', 'TL-250', 18000, 140, 0, 220, 4500000],
                ['Kato', 'NK-160', 14000, 110, 0, 180, 3800000],
                ['Terex', 'TC-2600', 20000, 150, 0, 240, 5000000],
                ['Zoomlion', 'ZTC250', 19000, 145, 0, 230, 4800000],
            ],
            'forklift' => [
                ['Toyota', '8FD25', 3800, 50, 0, 60, 650000],
                ['Toyota', '8FD30', 4400, 60, 0, 70, 700000],
                ['Toyota', '8FD50', 7200, 95, 0, 90, 950000],
                ['Heli', 'CPCD30', 4500, 55, 0, 65, 580000],
                ['Heli', 'CPCD50', 7200, 90, 0, 85, 850000],
                ['Hyster', 'H5.0FT', 7500, 90, 0, 85, 900000],
                ['Jungheinrich', 'EFG216', 4000, 45, 0, 55, 750000],
                ['Mitsubishi', 'FD30', 4500, 55, 0, 65, 620000],
            ],
            'telehandler' => [
                ['JLG', '1055', 10000, 105, 0, 120, 1600000],
                ['JLG', 'G12-55', 12000, 125, 0, 130, 1800000],
                ['Manitou', 'MT1440', 11500, 100, 0, 125, 1700000],
                ['JCB', '542-70', 10500, 105, 0, 110, 1650000],
                ['Merlo', 'Roto 45.21', 13000, 130, 0, 135, 1900000],
            ],
            'reach-stacker' => [
                ['Kalmar', 'DRF450', 48000, 380, 0, 500, 3800000],
                ['Linde', 'C4532', 45000, 350, 0, 480, 3500000],
                ['Sany', 'SRSC45', 46000, 360, 0, 490, 3400000],
            ],
            'motor-grader' => [
                ['Caterpillar', '140K', 16500, 197, 0, 380, 3200000],
                ['Caterpillar', '120K', 15500, 178, 0, 360, 3000000],
                ['Komatsu', 'GD511', 14500, 150, 0, 280, 2800000],
                ['Volvo', 'G930', 14800, 155, 0, 300, 2900000],
                ['XCMG', 'GR180', 15000, 160, 0, 290, 2500000],
                ['New Holland', 'RG170', 15000, 168, 0, 295, 2700000],
            ],
            'vibro-roller' => [
                ['Bomag', 'BW211-40', 9500, 118, 0, 250, 1900000],
                ['Bomag', 'BW226DH', 18000, 200, 0, 340, 2800000],
                ['Hamm', 'HD12', 11500, 125, 0, 260, 2100000],
                ['Wacker Neuson', 'RT82', 8000, 85, 0, 200, 1800000],
                ['Sakai', 'SV512', 12000, 130, 0, 270, 2200000],
            ],
            'road-roller' => [
                ['Bomag', 'BW177', 8500, 85, 0, 200, 1700000],
                ['Dynapac', 'CC1300', 9000, 100, 0, 210, 1750000],
                ['Sakai', 'TW500', 10000, 110, 0, 220, 1900000],
            ],
            'asphalt-paver' => [
                ['Caterpillar', 'AP655', 16000, 160, 0, 300, 4200000],
                ['Vogele', 'Super 1800', 14000, 140, 0, 280, 4500000],
                ['XCMG', 'RP952', 15000, 150, 0, 290, 3800000],
            ],
            'soil-compactor' => [
                ['Bomag', 'BW212D', 10000, 110, 0, 230, 1800000],
                ['Wacker Neuson', 'RT SC-2', 9500, 105, 0, 225, 1750000],
                ['Sakai', 'SV501', 10500, 115, 0, 240, 1900000],
                ['Dynapac', 'CA134', 11500, 120, 0, 245, 1950000],
            ],
            'rock-breaker' => [
                ['Soosan', 'SB140', 3100, 0, 0, 40, 1800000],
                ['Montabert', 'V1200', 2900, 0, 0, 38, 2100000],
                ['Atlas Copco', 'MB1500', 3200, 0, 0, 42, 2300000],
                ['Furukawa', 'F22', 2800, 0, 0, 36, 1700000],
            ],
            'backhoe-loader' => [
                ['JCB', '3CX', 8200, 92, 0, 180, 1500000],
                ['JCB', '4CX', 9200, 108, 0, 200, 1700000],
                ['Caterpillar', '428E', 8800, 95, 0, 190, 1600000],
                ['Caterpillar', '432F', 9500, 105, 0, 200, 1750000],
                ['Case', '580N', 8500, 90, 0, 185, 1550000],
                ['Komatsu', 'WB93R', 8700, 92, 0, 190, 1600000],
                ['LiuGong', '777', 8800, 95, 0, 195, 1400000],
                ['Sany', 'SY115', 8900, 96, 0, 200, 1450000],
            ],
            'water-tanker' => [
                ['Hino', '500 FM 260 JD', 17000, 260, 12, 300, 1600000],
                ['Isuzu', 'Giga 300', 18000, 300, 14, 320, 1700000],
                ['Mercedes-Benz', 'Actros 3340', 21000, 340, 16, 500, 2200000],
                ['Volvo', 'FMX440', 26000, 440, 18, 700, 2800000],
            ],
            'fuel-tanker' => [
                ['Mitsubishi FUSO', 'FV425', 20000, 280, 0, 400, 2000000],
                ['Hino', '500 FM', 18000, 260, 0, 350, 1900000],
                ['Isuzu', 'Giga', 19000, 280, 0, 360, 1950000],
            ],
            'lowbed-trailer' => [
                ['Hino', '700 Prime Mover', 20000, 360, 0, 400, 2600000],
                ['Volvo', 'FH16', 28000, 540, 0, 600, 3400000],
                ['Scania', 'R560', 24000, 560, 0, 600, 3200000],
                ['Sinotruk', 'HOWO 440', 20000, 440, 0, 400, 2400000],
            ],
            'pile-driver' => [
                ['MKT', 'V32', 18000, 150, 0, 300, 8000000],
                ['Junttan', 'PM26', 30000, 250, 0, 450, 12000000],
                ['IHC', 'S90', 26000, 220, 0, 420, 10500000],
            ],
            'concrete-pump' => [
                ['Putzmeister', 'BSF 36', 24000, 180, 0, 280, 5200000],
                ['Sany', 'HBT60', 16000, 130, 0, 240, 4200000],
                ['Schwing', 'BP3000', 18000, 140, 0, 250, 4500000],
            ],
        ];

        $locations = [
            'Jawa' => ['Jakarta', 'Bekasi', 'Karawang', 'Bandung', 'Semarang', 'Surabaya', 'Gresik', 'Cilegon', 'Bogor'],
            'Kalimantan' => ['Balikpapan', 'Samarinda', 'Banjarmasin', 'Pontianak', 'Berau', 'Palangkaraya'],
            'Sumatera' => ['Medan', 'Pekanbaru', 'Palembang', 'Lampung', 'Jambi', 'Padang'],
            'Sulawesi' => ['Makassar', 'Manado', 'Palu', 'Kendari'],
            'Papua' => ['Timika', 'Jayapura'],
            'Bali' => ['Denpasar'],
        ];

        $miningCategories = ['dump-truck', 'hydraulic-excavator', 'bulldozer', 'articulated-dump-truck', 'rock-breaker', 'water-tanker', 'fuel-tanker'];

        $index = 0;
        foreach ($fleet as $slug => $units) {
            $category = $categories[$slug];
            $prefix = $category->slug === 'hydraulic-excavator' ? 'EX' : strtoupper(substr(implode('', array_map(fn ($w) => substr($w, 0, 1), explode('-', $slug))), 0, 2));

            foreach ($units as [$brand, $model, $weight, $power, $bucket, $fuel, $rate]) {
                $index++;

                // Regional assignment: mining categories skew toward Kalimantan/Papua
                if (in_array($slug, $miningCategories)) {
                    $region = random_int(1, 10) <= 7 ? ['Kalimantan', 'Papua'][random_int(0, 1)] : ['Kalimantan', 'Papua', 'Sumatera', 'Sulawesi'][random_int(0, 3)];
                } else {
                    $region = random_int(1, 10) <= 6 ? 'Jawa' : ['Jawa', 'Sumatera', 'Kalimantan', 'Sulawesi', 'Bali'][random_int(0, 4)];
                }

                $city = $locations[$region][random_int(0, count($locations[$region]) - 1)];

                $year = random_int(2016, 2024);
                $hours = random_int(1500, 18000);
                $profile = (($index * 37) % 100) / 100;
                $status = 'available';

                $purchasePrice = $rate * random_int(320, 480);
                $monthlyRate = round($rate * 21);
                $weeklyRate = round($rate * 6);
                $deposit = round($monthlyRate * 1.2);
                $condition = $profile > 0.8 ? 'excellent' : (random_int(1, 5) === 1 ? 'fair' : 'good');

                Equipment::create([
                    'equipment_code' => $prefix . '-' . str_pad((string) $index, 3, '0', STR_PAD_LEFT),
                    'name' => "{$brand} {$model}",
                    'category_id' => $category->id,
                    'brand' => $brand,
                    'model' => $model,
                    'year' => $year,
                    'serial_number' => strtoupper(substr($brand, 0, 3)) . '-' . strtoupper(substr(md5((string) random_int(1, 999999)), 0, 8)),
                    'operating_weight' => $weight,
                    'engine_power' => $power,
                    'bucket_capacity' => $bucket ?: null,
                    'fuel_capacity' => $fuel ?: null,
                    'operating_hours' => $hours,
                    'current_location' => "{$city}, {$region}",
                    'city' => $city,
                    'province' => $region,
                    'region' => $region,
                    'condition' => $condition,
                    'status' => $status,
                    'daily_rate' => $rate,
                    'weekly_rate' => $weeklyRate,
                    'monthly_rate' => $monthlyRate,
                    'deposit' => $deposit,
                    'purchase_price' => $purchasePrice,
                    'purchase_date' => now()->subYears(now()->year - $year)->subMonths(random_int(1, 11)),
                    'next_service_hours' => $hours + random_int(150, 600),
                    'hourly_rate' => round($rate / 8),
                    'description' => "{$brand} {$model} in {$condition} condition. Ready for immediate mobilization to {$city}.",
                    'created_at' => now()->subMonths(random_int(6, 30)),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}