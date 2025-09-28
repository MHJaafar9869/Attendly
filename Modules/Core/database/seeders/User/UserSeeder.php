<?php

namespace Modules\Core\database\seeders\User;

use Illuminate\Database\Seeder;
use Modules\Core\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate([
            'name' => 'Sample name 1',
            'email' => 'user1@example.com',
            'email_verified_at' => now(),
            'phone' => 'Sample phone 1',
            'address' => 'Sample address 1',
            'password' => bcrypt('password123'),
            'role_id' => '1',
            'is_active' => true,
            'type_id' => '1',
            'status_id' => '1',
            'remember_token' => 'Sample remember_token 1',
        ]);

        User::firstOrCreate([
            'name' => 'Sample name 2',
            'email' => 'user2@example.com',
            'email_verified_at' => now(),
            'phone' => 'Sample phone 2',
            'address' => 'Sample address 2',
            'password' => bcrypt('password123'),
            'role_id' => '2',
            'is_active' => true,
            'type_id' => '2',
            'status_id' => '2',
            'remember_token' => 'Sample remember_token 2',
        ]);

        User::firstOrCreate([
            'name' => 'Sample name 3',
            'email' => 'user3@example.com',
            'email_verified_at' => now(),
            'phone' => 'Sample phone 3',
            'address' => 'Sample address 3',
            'password' => bcrypt('password123'),
            'role_id' => '3',
            'is_active' => true,
            'type_id' => '3',
            'status_id' => '3',
            'remember_token' => 'Sample remember_token 3',
        ]);

        User::firstOrCreate([
            'name' => 'Sample name 4',
            'email' => 'user4@example.com',
            'email_verified_at' => now(),
            'phone' => 'Sample phone 4',
            'address' => 'Sample address 4',
            'password' => bcrypt('password123'),
            'role_id' => '4',
            'is_active' => true,
            'type_id' => '4',
            'status_id' => '4',
            'remember_token' => 'Sample remember_token 4',
        ]);

        User::firstOrCreate([
            'name' => 'Sample name 5',
            'email' => 'user5@example.com',
            'email_verified_at' => now(),
            'phone' => 'Sample phone 5',
            'address' => 'Sample address 5',
            'password' => bcrypt('password123'),
            'role_id' => '5',
            'is_active' => true,
            'type_id' => '5',
            'status_id' => '5',
            'remember_token' => 'Sample remember_token 5',
        ]);

    }
}
