<?php

namespace Modules\Core\database\seeders\Setting;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate([
            'key' => 'Sample key 1',
            'value' => 'Sample value 1',
            'type' => 'Sample type 1',
            'meta' => json_encode(['sample' => 'Sample meta 1']),
        ]);

        Setting::firstOrCreate([
            'key' => 'Sample key 2',
            'value' => 'Sample value 2',
            'type' => 'Sample type 2',
            'meta' => json_encode(['sample' => 'Sample meta 2']),
        ]);

        Setting::firstOrCreate([
            'key' => 'Sample key 3',
            'value' => 'Sample value 3',
            'type' => 'Sample type 3',
            'meta' => json_encode(['sample' => 'Sample meta 3']),
        ]);

        Setting::firstOrCreate([
            'key' => 'Sample key 4',
            'value' => 'Sample value 4',
            'type' => 'Sample type 4',
            'meta' => json_encode(['sample' => 'Sample meta 4']),
        ]);

        Setting::firstOrCreate([
            'key' => 'Sample key 5',
            'value' => 'Sample value 5',
            'type' => 'Sample type 5',
            'meta' => json_encode(['sample' => 'Sample meta 5']),
        ]);

    }
}
