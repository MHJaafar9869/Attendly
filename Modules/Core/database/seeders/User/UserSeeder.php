<?php

namespace Modules\Core\database\seeders\User;

use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Core\Models\Role;
use Modules\Core\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            // === Admin user (Filament login) ===
            $user = User::firstOrCreate(
                ['email' => 'mostafajaafar9869@gmail.com'],
                [
                    'first_name' => 'Mostafa',
                    'last_name' => 'Jaafar',
                    'slug_name' => 'mostafa-jaafar-' . Str::random(8),
                    'status_id' => 2,
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                    'token_version' => 1,
                    'remember_token' => Str::random(10),
                ]
            );

            $roleId = Role::where('name', 'super_admin')->pluck('id');
            $user->roles()->attach($roleId);

            // === Additional users for testing ===
            $users = [
                [
                    'first_name' => 'Lina',
                    'last_name' => 'Adel',
                    'slug_name' => 'lina-adel-' . Str::random(8),
                    'email' => 'user1@example.com',
                ],
                [
                    'first_name' => 'Karim',
                    'last_name' => 'Maged',
                    'slug_name' => 'karim-maged-' . Str::random(8),
                    'email' => 'user2@example.com',
                ],
                [
                    'first_name' => 'Nour',
                    'last_name' => 'Hassan',
                    'slug_name' => 'nour-hassan-' . Str::random(8),
                    'email' => 'user3@example.com',
                ],
            ];

            foreach ($users as $data) {
                User::firstOrCreate(
                    ['email' => $data['email']],
                    [
                        ...$data,
                        'status_id' => 1,
                        'password' => Hash::make('password123'),
                        'token_version' => 1,
                        'remember_token' => Str::random(10),
                    ]
                );
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
    }
}
