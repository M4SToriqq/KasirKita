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
                'name' => 'Toriq Habil Fadhila',
                'username' => 'Toriq',
                'email' => 'toriqqhabilfadhila21@gmail.com',
                'phone' => '082245222599',
                'role' => 'owner',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Renata Rizki Andini',
                'username' => 'Renata',
                'email' => 'kasir1@pos.com',
                'phone' => '081234567891',
                'role' => 'kasir',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Putra Satrio Nugraha',
                'username' => 'Putra',
                'email' => 'kasir2@pos.com',
                'phone' => '081234567892',
                'role' => 'kasir',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Staff Inventory',
                'username' => 'inventory',
                'email' => 'inventory@pos.com',
                'phone' => '081234567893',
                'role' => 'inventory',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
