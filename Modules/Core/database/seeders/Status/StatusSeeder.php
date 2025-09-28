<?php

namespace Modules\Core\database\seeders\Status;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        Status::firstOrCreate([
            'name' => 'Pending Verification',
            'context' => 'user',
            'color' => '#f59e0b', // amber
        ]);

        Status::firstOrCreate([
            'name' => 'Active',
            'context' => 'user',
            'color' => '#10b981', // green
        ]);

        Status::firstOrCreate([
            'name' => 'Inactive',
            'context' => 'user',
            'color' => '#6b7280', // gray
        ]);

        Status::firstOrCreate([
            'name' => 'Suspended',
            'context' => 'user',
            'color' => '#ef4444', // red
        ]);

        Status::firstOrCreate([
            'name' => 'Banned',
            'context' => 'user',
            'color' => '#7f1d1d', // dark red
        ]);
    }
}
