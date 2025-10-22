<?php

namespace Modules\Domain\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Domain\database\seeders\Governorate\GovernorateSeeder;
use Modules\Domain\database\seeders\Student\StudentSeeder;

class DomainDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            GovernorateSeeder::class,
            StudentSeeder::class,
        ]);
    }
}
