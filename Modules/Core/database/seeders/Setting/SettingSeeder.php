<?php

namespace Modules\Core\database\seeders\Setting;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $settings = [
            // ...
        ];

        data_set($settings, '*.created_at', $now);
        data_set($settings, '*.updated_at', $now);

        DB::table('settings')->upsert(
            $settings,
            ['key'],
            ['key', 'value', 'type', 'description', 'updated_at']
        );
    }
}
