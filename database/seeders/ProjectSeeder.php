<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            // name, industry, region, city, status, duration months
            ['New Town Development', 'Construction', 'Jawa', 'Jakarta', 28],
            ['Residential Tower Complex', 'Construction', 'Jawa', 'Jakarta', 22],
            ['Industrial Park Phase II', 'Industrial', 'Jawa', 'Karawang', 18],
            ['Coal Hauling Road Upgrade', 'Mining', 'Kalimantan', 'Balikpapan', 24],
            ['Open Pit Mine Development', 'Mining', 'Kalimantan', 'Samarinda', 30],
            ['Nickel Mine Expansion', 'Mining', 'Sulawesi', 'Kendari', 26],
            ['Gold Processing Plant', 'Mining', 'Papua', 'Timika', 32],
            ['Toll Road Section 5', 'Infrastructure', 'Jawa', 'Bekasi', 26],
            ['Bridge Crossing Project', 'Infrastructure', 'Sumatera', 'Palembang', 20],
            ['Port Terminal Expansion', 'Infrastructure', 'Jawa', 'Semarang', 24],
            ['Dam Construction Project', 'Infrastructure', 'Sulawesi', 'Palu', 36],
            ['Airport Runway Extension', 'Infrastructure', 'Jawa', 'Surabaya', 18],
            ['Palm Oil Plantation Road', 'Plantation', 'Sumatera', 'Pekanbaru', 16],
            ['Estate Development West', 'Plantation', 'Kalimantan', 'Pontianak', 20],
            ['Riau Plantation Expansion', 'Plantation', 'Sumatera', 'Riau', 18],
            ['Geothermal Power Plant', 'Energy', 'Jawa', 'Bandung', 30],
            ['Solar Farm Site Works', 'Energy', 'Jawa', 'Cilegon', 14],
            ['Gas Pipeline Corridor', 'Energy', 'Sumatera', 'Jambi', 28],
            ['Warehouse Logistics Hub', 'Industrial', 'Jawa', 'Tangerang', 16],
            ['Steel Manufacturing Plant', 'Industrial', 'Jawa', 'Cilegon', 22],
            ['Cement Plant Upgrade', 'Industrial', 'Jawa', 'Gresik', 20],
            ['Highway Maintenance Works', 'Infrastructure', 'Jawa', 'Semarang', 12],
            ['River Dredging Project', 'Infrastructure', 'Sumatera', 'Medan', 18],
            ['Coal Port Stockyard', 'Mining', 'Kalimantan', 'Banjar', 20],
            ['Granite Quarry Operation', 'Mining', 'Sumatera', 'Lampung', 24],
            ['Apartment Construction', 'Construction', 'Jawa', 'Surabaya', 20],
            ['Mall & Mixed Use Complex', 'Construction', 'Jawa', 'Bandung', 24],
            ['Industrial Estate Road Works', 'Construction', 'Sumatera', 'Medan', 14],
            ['Water Treatment Plant', 'Infrastructure', 'Jawa', 'Surabaya', 16],
            ['Land Clearing & Grading', 'Plantation', 'Kalimantan', 'Berau', 15],
            ['LNG Terminal Works', 'Energy', 'Papua', 'Fakfak', 26],
            ['Nusantara IKN Support', 'Infrastructure', 'Kalimantan', 'Nusantara', 30],
            ['Dam Safety Rehabilitation', 'Infrastructure', 'Jawa', 'Solo', 14],
            ['Smelter Construction', 'Mining', 'Sulawesi', 'Morowali', 34],
        ];

        $customers = Customer::where('status', 'active')->get();
        $templates = array_slice($templates, 0, 30);

        foreach ($templates as $i => [$name, $industry, $region, $city, $durationMonths]) {
            $customer = $customers->random();

            $start = now()->subMonths(random_int(1, $durationMonths))->startOfMonth();
            $end = $start->copy()->addMonths($durationMonths);

            $monthlyValue = match ($industry) {
                'Mining' => random_int(450000000, 1400000000),
                'Energy' => random_int(350000000, 900000000),
                'Infrastructure' => random_int(300000000, 800000000),
                'Plantation' => random_int(150000000, 400000000),
                'Industrial' => random_int(250000000, 700000000),
                default => random_int(200000000, 600000000),
            };

            $status = now()->gt($end) ? 'completed' : (random_int(1, 12) === 1 ? 'on_hold' : 'active');

            Project::create([
                'project_code' => 'PRJ-' . strtoupper(substr($industry, 0, 3)) . '-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'name' => $name,
                'customer_id' => $customer->id,
                'industry' => $industry,
                'location' => "{$city}",
                'city' => $city,
                'province' => $region,
                'region' => $region,
                'start_date' => $start,
                'end_date' => $end,
                'contract_value' => $monthlyValue * $durationMonths,
                'status' => $status,
                'description' => "{$name} in {$city} — deployed fleet: " . random_int(3, 18) . " units across " . random_int(2, 5) . " equipment categories.",
                'created_at' => $start->subMonths(random_int(1, 3)),
                'updated_at' => now(),
            ]);
        }
    }
}