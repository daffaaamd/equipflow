<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\Operator;
use App\Models\Project;
use Illuminate\Database\Seeder;

class OperatorSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Slamet Riyadi', 'Agus Supriyanto', 'Bambang Pamungkas', 'Cahyo Utomo', 'Dedi Irawan',
            'Eko Susilo', 'Fajar Nugraha', 'Gatot Subroto', 'Haryanto', 'Iwan Setiawan',
            'Joko Widodo', 'Karyanto', 'Lukas Wijaya', 'Maman Suherman', 'Nurdin Halim',
            'Oky Pratama', 'Purnomo', 'Rahmat Hidayat', 'Sutrisno', 'Taufik Hidayat',
            'Ujang Komarudin', 'Wawan Setiawan', 'Yanto', 'Zulkifli', 'Adi Firmansyah',
            'Bayu Aji', 'Candra Kirana', 'Doni Setiawan', 'Edi Kurniawan', 'Firman Syah',
            'Galih Prakoso', 'Hendra Saputra', 'Irfan Bachdim', 'Joko Tri', 'Kurniawan',
            'Lutfi Hakim', 'Mulyadi', 'Nur Hidayat', 'Oman Fathurahman', 'Panji Wirawan',
        ];

        $certifications = ['Heavy Equipment Operator - Excavator', 'Heavy Equipment Operator - Bulldozer', 'Heavy Equipment Operator - Wheel Loader', 'Crane Operator SIO', 'Dump Truck Driver', 'Rigger & Signalman', 'Forklift Operator'];
        $equipment = Equipment::all();
        $projects = Project::whereIn('status', ['planning', 'active'])->get();

        foreach ($names as $i => $name) {
            $cert = $certifications[random_int(0, count($certifications) - 1)];
            $expiry = now()->addMonths(random_int(-4, 24));

            Operator::create([
                'operator_code' => 'OPR-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@equipflow.co.id',
                'phone' => '081' . random_int(2, 9) . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'certification' => $cert,
                'certification_expiry' => $expiry,
                'license_number' => 'SIO-' . random_int(1000000, 9999999),
                'years_experience' => random_int(2, 18),
                'assigned_equipment_id' => $equipment->random()->id,
                'project_id' => random_int(1, 4) === 1 ? $projects->random()->id : null,
                'working_hours' => random_int(800, 2400),
                'availability' => random_int(1, 5) === 1 ? 'available' : 'assigned',
                'status' => random_int(1, 12) === 1 ? 'inactive' : 'active',
                'notes' => random_int(1, 3) === 1 ? 'Certified and cleared for site work.' : null,
                'created_at' => now()->subMonths(random_int(2, 30)),
                'updated_at' => now(),
            ]);
        }
    }
}