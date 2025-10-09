<?php

namespace Modules\Core\database\seeders\Permission;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Permission::firstOrCreate([
            'name' => 'Sample name 1',
        ]);

        Permission::firstOrCreate([
            'name' => 'Sample name 2',
        ]);

        Permission::firstOrCreate([
            'name' => 'Sample name 3',
        ]);

        Permission::firstOrCreate([
            'name' => 'Sample name 4',
        ]);

        Permission::firstOrCreate([
            'name' => 'Sample name 5',
        ]);

    }
}
