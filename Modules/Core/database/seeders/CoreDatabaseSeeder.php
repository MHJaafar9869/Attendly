<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\database\seeders\Permission\PermissionSeeder;
use Modules\Core\database\seeders\Role\RoleSeeder;
use Modules\Core\database\seeders\Setting\SettingSeeder;
use Modules\Core\database\seeders\Status\StatusSeeder;
use Modules\Core\database\seeders\Type\TypeSeeder;
use Modules\Core\database\seeders\User\UserSeeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call(TypeSeeder::class);
        $this->call([
            // StripePaymentSeeder::class,
            // PayPalPaymentSeeder::class,
            SettingSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            TypeSeeder::class,
            StatusSeeder::class,
            UserSeeder::class,
        ]);
    }
}
