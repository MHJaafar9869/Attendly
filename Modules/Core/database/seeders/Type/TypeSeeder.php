<?php

namespace Modules\Core\database\seeders\Type;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Type;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        Type::firstOrCreate([
            'name' => 'Sample name 1',
            'context' => 'Sample context 1',
            'color' => 'Sample color 1',
        ]);

        Type::firstOrCreate([
            'name' => 'Sample name 2',
            'context' => 'Sample context 2',
            'color' => 'Sample color 2',
        ]);

        Type::firstOrCreate([
            'name' => 'Sample name 3',
            'context' => 'Sample context 3',
            'color' => 'Sample color 3',
        ]);

        Type::firstOrCreate([
            'name' => 'Sample name 4',
            'context' => 'Sample context 4',
            'color' => 'Sample color 4',
        ]);

        Type::firstOrCreate([
            'name' => 'Sample name 5',
            'context' => 'Sample context 5',
            'color' => 'Sample color 5',
        ]);

    }
}
