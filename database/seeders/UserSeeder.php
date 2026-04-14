<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Admin account
        $admin = User::updateOrCreate(
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

        // Seed default address for admin
        UserAddress::updateOrCreate(
            ['user_id' => $admin->id, 'is_default' => true],
            [
                'receiver_name' => $admin->full_name,
                'receiver_phone' => $admin->phone,
                'address' => $admin->address,
                'commune' => $admin->commune,
                'city' => $admin->city,
            ]
        );

        // 2. Sample Customer account
        $customer = User::updateOrCreate(
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

        // Seed default address for customer
        UserAddress::updateOrCreate(
            ['user_id' => $customer->id, 'is_default' => true],
            [
                'receiver_name' => $customer->full_name,
                'receiver_phone' => $customer->phone,
                'address' => $customer->address,
                'commune' => $customer->commune,
                'city' => $customer->city,
            ]
        );
    }
}
