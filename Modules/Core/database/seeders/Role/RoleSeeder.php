<?php

namespace Modules\Core\database\seeders\Role;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate([
            'name' => 'super_admin',
        ]);

        Role::firstOrCreate([
            'name' => 'admin',
        ]);

        Role::firstOrCreate([
            'name' => 'moderator',
        ]);

        Role::firstOrCreate([
            'name' => 'user',
        ]);

        Role::firstOrCreate([
            'name' => 'guest',
        ]);
    }
}
