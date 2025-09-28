<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call(Setting\SettingSeeder::class);
        // $this->call(Permission\PermissionSeeder::class);
        // $this->call(Type\TypeSeeder::class);
        $this->call(Status\StatusSeeder::class);
        $this->call(Role\RoleSeeder::class);
        // $this->call(User\UserSeeder::class);
        // $this->call([]);
    }
}
