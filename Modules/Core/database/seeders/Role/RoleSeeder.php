<?php

namespace Modules\Core\database\seeders\Role;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'Super Admin',
        ]);

        Role::firstOrCreate([
            'name' => 'Admin',
        ]);

        Role::firstOrCreate([
            'name' => 'Moderator',
        ]);

        Role::firstOrCreate([
            'name' => 'User',
        ]);

        Role::firstOrCreate([
            'name' => 'Guest',
        ]);
    }
}
