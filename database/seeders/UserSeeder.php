<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin account
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'password' => 'admin123', // Will be hashed via User model cast
                'full_name' => 'Hệ thống Quản trị',
                'email' => 'admin@morico.vn',
                'phone' => '0900000000',
                'address' => '30 Lê Lợi',
                'commune' => 'Bến Nghé',
                'city' => 'Quận 1, TP.HCM',
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        // 2. Sample Customer account
        User::updateOrCreate(
            ['username' => 'customer'],
            [
                'password' => 'user123',
                'full_name' => 'Nguyễn Văn Khách',
                'email' => 'khachhang@gmail.com',
                'phone' => '0911222333',
                'address' => '123 Đường ABC',
                'commune' => 'Phường 5',
                'city' => 'Quận 3, TP.HCM',
                'role' => 'customer',
                'is_active' => true,
            ]
        );
    }
}
