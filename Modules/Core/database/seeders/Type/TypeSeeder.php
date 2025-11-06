<?php

namespace Modules\Core\database\seeders\Type;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TypeSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $records = [
            [
                'name' => 'Sample name 1',
                'context' => 'Sample context 1',
                'text_color' => 'Sample text_color 1',
                'bg_color' => 'Sample bg_color 1',
                'description' => 'Sample description 1',
            ],
            [
                'name' => 'Sample name 2',
                'context' => 'Sample context 2',
                'text_color' => 'Sample text_color 2',
                'bg_color' => 'Sample bg_color 2',
                'description' => 'Sample description 2',
            ],
            [
                'name' => 'Sample name 3',
                'context' => 'Sample context 3',
                'text_color' => 'Sample text_color 3',
                'bg_color' => 'Sample bg_color 3',
                'description' => 'Sample description 3',
            ],
            [
                'name' => 'Sample name 4',
                'context' => 'Sample context 4',
                'text_color' => 'Sample text_color 4',
                'bg_color' => 'Sample bg_color 4',
                'description' => 'Sample description 4',
            ],
            [
                'name' => 'Sample name 5',
                'context' => 'Sample context 5',
                'text_color' => 'Sample text_color 5',
                'bg_color' => 'Sample bg_color 5',
                'description' => 'Sample description 5',
            ],
        ];

        data_set($records, '*.created_at', $now);
        data_set($records, '*.updated_at', $now);

        DB::table('types')->upsert(
            $records,
            [
                'id',
            ],
            [
                'name',
                'context',
                'text_color',
                'bg_color',
                'description',
                'updated_at',
            ]
        );
    }
}
