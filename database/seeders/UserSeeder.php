<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Owner Toko',
                'username' => 'owner',
                'email' => 'owner@pos.com',
                'phone' => '081234567890',
                'role' => 'owner',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Kasir 1',
                'username' => 'kasir1',
                'email' => 'kasir1@pos.com',
                'phone' => '081234567891',
                'role' => 'kasir',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Kasir 2',
                'username' => 'kasir2',
                'email' => 'kasir2@pos.com',
                'phone' => '081234567892',
                'role' => 'kasir',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
            [
                'name' => 'Staff Inventory',
                'username' => 'inventory',
                'email' => 'inventory@pos.com',
                'phone' => '081234567893',
                'role' => 'inventory',
                'password' => Hash::make('password'),
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
