<?php

namespace Modules\Core\database\seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // === Admin user (Filament login) ===
        User::firstOrCreate(
            ['email' => 'mostafajaafar9869@gmail.com'],
            [
                'first_name' => 'Mostafa',
                'last_name' => 'Jaafar',
                'slug_name' => 'mostafa-jaafar',
                'phone' => '+201097329869',
                'address' => '123 Sample Street',
                'city' => 'Cairo',
                'country' => 'Egypt',
                'status_id' => 2,
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'device' => 'Desktop Chrome',
                'token_version' => 1,
                'remember_token' => Str::random(10),
            ]
        );

        // === Additional users for testing ===
        $users = [
            [
                'first_name' => 'Lina',
                'last_name' => 'Adel',
                'slug_name' => 'lina-adel',
                'email' => 'user1@example.com',
                'phone' => '+201000000001',
            ],
            [
                'first_name' => 'Karim',
                'last_name' => 'Maged',
                'slug_name' => 'karim-maged',
                'email' => 'user2@example.com',
                'phone' => '+201000000002',
            ],
            [
                'first_name' => 'Nour',
                'last_name' => 'Hassan',
                'slug_name' => 'nour-hassan',
                'email' => 'user3@example.com',
                'phone' => '+201000000003',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    ...$data,
                    'address' => 'Sample Address',
                    'city' => 'Alexandria',
                    'country' => 'Egypt',
                    'status_id' => 1,
                    'password' => Hash::make('password123'),
                    'device' => 'Android',
                    'token_version' => 1,
                    'remember_token' => Str::random(10),
                ]
            );
        }
    }
}
