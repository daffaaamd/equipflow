<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            // Strategic / high-value repeat customers
            ['PT Nusantara Konstruksi', 'Construction', 'Jawa', 'Jakarta', 'strategic'],
            ['PT Bangun Cipta Sarana', 'Construction', 'Jawa', 'Bandung', 'strategic'],
            ['PT Tambang Bara Utama', 'Mining', 'Kalimantan', 'Balikpapan', 'strategic'],
            ['PT Karya Energi Persada', 'Energy', 'Jawa', 'Surabaya', 'strategic'],
            ['PT Perkebunan Sawit Nusantara', 'Plantation', 'Sumatera', 'Pekanbaru', 'strategic'],
            ['PT Jalan Tol Indonesia', 'Infrastructure', 'Jawa', 'Jakarta', 'strategic'],
            ['PT Aneka Tambang Sejahtera', 'Mining', 'Sulawesi', 'Makassar', 'high_value'],
            ['PT Citra Land Development', 'Property', 'Jawa', 'Tangerang', 'high_value'],
            ['PT Graha Infra Jaya', 'Infrastructure', 'Sumatera', 'Medan', 'high_value'],
            ['PT Pelabuhan Nusantara', 'Infrastructure', 'Jawa', 'Semarang', 'high_value'],
            ['PT Mitra Abadi Karya', 'Construction', 'Kalimantan', 'Samarinda', 'high_value'],
            ['PT Semen Nusantara Tbk', 'Industrial', 'Jawa', 'Gresik', 'high_value'],

            // Medium value customers
            ['PT Karya Bangun Sejahtera', 'Construction', 'Jawa', 'Yogyakarta', 'medium_value'],
            ['PT Delta Prima Konstruksi', 'Construction', 'Sulawesi', 'Manado', 'medium_value'],
            ['PT Bumi Raya Utama', 'Mining', 'Kalimantan', 'Banjarmasin', 'medium_value'],
            ['PT Kaltim Energi', 'Energy', 'Kalimantan', 'Balikpapan', 'medium_value'],
            ['PT Pulau Mas Perkasa', 'Plantation', 'Sumatera', 'Palembang', 'medium_value'],
            ['PT Rimba Sejahtera', 'Plantation', 'Kalimantan', 'Pontianak', 'medium_value'],
            ['PT Mandiri Jaya Konstruksi', 'Construction', 'Jawa', 'Bekasi', 'medium_value'],
            ['PT Persada Mining Service', 'Mining', 'Papua', 'Timika', 'medium_value'],
            ['PT Bangun Infra Mandiri', 'Infrastructure', 'Jawa', 'Solo', 'medium_value'],
            ['PT Wijaya Karya Beton', 'Industrial', 'Jawa', 'Bogor', 'medium_value'],
            ['PT Agung Sedayu Group', 'Property', 'Jawa', 'Jakarta', 'medium_value'],
            ['PT Adhi Karya Bangunan', 'Construction', 'Jawa', 'Jakarta', 'medium_value'],
            ['PT Bukit Asam Mitra', 'Mining', 'Sumatera', 'Tanjung Enim', 'medium_value'],
            ['PT Cipta Baja Konstruksi', 'Construction', 'Jawa', 'Surabaya', 'medium_value'],
            ['PT Terang Bumi Energi', 'Energy', 'Sulawesi', 'Palu', 'medium_value'],
            ['PT Tambang Emas Nusantara', 'Mining', 'Sulawesi', 'Kendari', 'medium_value'],
            ['PT Sinar Mas Agro', 'Plantation', 'Sumatera', 'Medan', 'medium_value'],
            ['PT Jembatan Nusantara', 'Infrastructure', 'Kalimantan', 'Banjarbaru', 'medium_value'],

            // Low value / occasional customers
            ['PT Karya Utama Mandiri', 'Construction', 'Jawa', 'Cirebon', 'low_value'],
            ['PT Sukses Bersama', 'Construction', 'Sumatera', 'Lampung', 'low_value'],
            ['PT Bangun Persada Nusantara', 'Construction', 'Jawa', 'Malang', 'low_value'],
            ['PT Inti Karya Sejahtera', 'Construction', 'Bali', 'Denpasar', 'low_value'],
            ['PT Cahaya Baru Konstruksi', 'Construction', 'Jawa', 'Purwokerto', 'low_value'],
            ['PT Global Mining Utama', 'Mining', 'Kalimantan', 'Tarakan', 'low_value'],
            ['PT Energi Nusantara', 'Energy', 'Jawa', 'Cilegon', 'low_value'],
            ['PT Agro Indah Perkasa', 'Plantation', 'Sumatera', 'Jambi', 'low_value'],
            ['PT Perkebunan Sumatera', 'Plantation', 'Sumatera', 'Medan', 'low_value'],
            ['PT Karya Infrastruktur', 'Infrastructure', 'Jawa', 'Tegal', 'low_value'],
            ['PT Properti Nusantara', 'Property', 'Jawa', 'Depok', 'low_value'],
            ['PT Graha Bangun Utama', 'Construction', 'Sulawesi', 'Gorontalo', 'low_value'],
            ['PT Nusantara Steel', 'Industrial', 'Jawa', 'Cilegon', 'low_value'],
            ['PT Karunia Jaya Abadi', 'Construction', 'Sumatera', 'Padang', 'low_value'],
            ['PT Bakti Karya Mandiri', 'Construction', 'Jawa', 'Serang', 'low_value'],
            ['PT Surya Kencana Group', 'Industrial', 'Jawa', 'Sidoarjo', 'low_value'],
            ['PT Gemilang Konstruksi', 'Construction', 'Kalimantan', 'Palangkaraya', 'low_value'],
            ['PT Riau Agro Mandiri', 'Plantation', 'Sumatera', 'Pekanbaru', 'low_value'],
            ['PT Berkah Jaya Tani', 'Plantation', 'Sulawesi', 'Luwuk', 'low_value'],
            ['PT Mitra Karya Bangun', 'Construction', 'Jawa', 'Kediri', 'low_value'],
        ];

        $contacts = [
            'Ahmad Fauzi', 'Bambang Sutrisno', 'Candra Wijaya', 'Dedi Kurniawan', 'Eko Prasetyo',
            'Firman Hidayat', 'Gunawan Setiawan', 'Hendra Gunawan', 'Ivan Ramadhan', 'Joko Susilo',
            'Kurnia Sandi', 'Lukman Hakim', 'Muhammad Rizal', 'Nanda Saputra', 'Oscar Tambunan',
            'Putra Ramadhan', 'Rahmat Hidayat', 'Slamet Riyadi', 'Teguh Wibowo', 'Umar Said',
            'Vino Pratama', 'Wahyu Nugroho', 'Yudi Hartono', 'Zainal Abidin', 'Agus Salim',
            'Bayu Anggara', 'Cahyo Nugroho', 'Dimas Anggara', 'Edi Susanto', 'Fajar Rizal',
            'Gilang Ramadhan', 'Haris Firmansyah', 'Indra Gunawan', 'Jefri Ardian', 'Krisna Wijaya',
        ];

        foreach ($companies as $i => [$name, $industry, $region, $city, $segment]) {
            $code = 'CUS-' . str_pad((string) ($i + 2), 4, '0', STR_PAD_LEFT);

            $contact = $contacts[$i % count($contacts)];
            $domain = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', str_replace(['PT ', 'Tbk', 'Group'], '', $name))) ?: 'company';

            Customer::updateOrCreate(
                ['customer_code' => $code],
                [
                    'company_name' => $name,
                    'contact_person' => $contact,
                    'email' => strtolower(str_replace(' ', '.', $contact)) . "@{$domain}.co.id",
                    'phone' => '081' . random_int(2, 9) . str_pad((string) random_int(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'address' => "Jl. Raya Industri No. " . random_int(1, 500) . ", {$city}",
                    'city' => $city,
                    'province' => $region,
                    'region' => $region,
                    'industry' => $industry,
                    'tax_id' => (string) random_int(1000000000, 9999999999),
                    'segment' => $segment,
                    'status' => random_int(1, 15) === 1 ? 'inactive' : 'active',
                ]
            );
        }
    }
}