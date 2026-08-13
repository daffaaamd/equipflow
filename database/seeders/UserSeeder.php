<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Andi Prasetyo', 'email' => 'admin@equipflow.com', 'role' => 'admin'],
            ['name' => 'Rina Kartika', 'email' => 'sales@equipflow.com', 'role' => 'sales'],
            ['name' => 'Budi Santoso', 'email' => 'operations@equipflow.com', 'role' => 'operations'],
            ['name' => 'Dewi Lestari', 'email' => 'maintenance@equipflow.com', 'role' => 'maintenance'],
            ['name' => 'Fajar Nugroho', 'email' => 'finance@equipflow.com', 'role' => 'finance'],
            ['name' => 'Customer Demo', 'email' => 'customer@equipflow.com', 'role' => 'customer', 'company_name' => 'PT Nusantara Konstruksi'],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => 'password',
                    'role' => $u['role'],
                    'phone' => '0812' . str_pad((string) random_int(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                    'company_name' => $u['company_name'] ?? null,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );

            if ($u['role'] === 'customer') {
                Customer::firstOrCreate(
                    ['email' => $u['email']],
                    [
                        'customer_code' => 'CUS-0001',
                        'user_id' => $user->id,
                        'company_name' => $u['company_name'],
                        'contact_person' => $u['name'],
                        'email' => $u['email'],
                        'phone' => $user->phone,
                        'region' => 'Jawa',
                        'city' => 'Jakarta',
                        'industry' => 'Construction',
                        'segment' => 'strategic',
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}