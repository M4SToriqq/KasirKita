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
                'name' => 'Owner',
                'username' => 'owner',
                'email' => 'owner@kasirkita.com',
                'phone' => '082245222599',
                'role' => 'owner',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Kasir1',
                'username' => 'kasir1',
                'email' => 'kasir1@kasirkita.com',
                'phone' => '081234567891',
                'role' => 'kasir',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Kasir2',
                'username' => 'kasir2',
                'email' => 'kasir2@kasirkita.com',
                'phone' => '081234567892',
                'role' => 'kasir',
                'password' => Hash::make('Password'),
                'is_active' => true,
            ],
            [
                'name' => 'Staff Inventory',
                'username' => 'inventory',
                'email' => 'inventory@kasirkita.com',
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
