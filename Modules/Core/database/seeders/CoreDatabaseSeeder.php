<?php

namespace Modules\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\database\seeders\Role\RoleSeeder;
use Modules\Core\database\seeders\Status\StatusSeeder;
use Modules\Core\database\seeders\User\UserSeeder;

class CoreDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $this->call(PayPalPayment\PayPalPaymentSeeder::class);
        // $this->call(Payment\StripePaymentSeeder::class);
        // $this->call(Setting\SettingSeeder::class);
        // $this->call(Permission\PermissionSeeder::class);
        // $this->call(Type\TypeSeeder::class);
        $this->call(StatusSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
