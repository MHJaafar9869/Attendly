<?php

namespace Modules\Core\database\seeders\Role;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $roles = [
            ['name' => 'super_admin'], // 1
            ['name' => 'admin'], // 2
            ['name' => 'accountant'], // 3
            ['name' => 'teacher'], // 4
            ['name' => 'supervisor'], // 5
            ['name' => 'student'], // 6
        ];

        data_set($roles, '*.created_at', $now);
        data_set($roles, '*.updated_at', $now);

        DB::table('roles')->upsert(
            $roles,
            ['name'],
            ['name', 'updated_at']
        );
    }
}
