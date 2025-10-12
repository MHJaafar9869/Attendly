<?php

namespace Modules\Core\database\seeders\Status;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [ // ID: 1
                'name' => 'pending_verification',
                'context' => 'user',
                'bg_color' => '#f59e0b', // amber
                'text_color' => '#78350f', // darker amber
            ],
            [ // ID: 2
                'name' => 'active',
                'context' => 'user',
                'bg_color' => '#10b981', // green
                'text_color' => '#064e3b', // darker green
            ],
            [ // ID: 3
                'name' => 'inactive',
                'context' => 'user',
                'bg_color' => '#6b7280', // gray
                'text_color' => '#1f2937', // dark gray
            ],
            [ // ID: 4
                'name' => 'suspended',
                'context' => 'user',
                'bg_color' => '#ef4444', // red
                'text_color' => '#7f1d1d', // deep red
            ],
            [ // ID: 5
                'name' => 'banned',
                'context' => 'user',
                'bg_color' => '#7f1d1d', // dark red
                'text_color' => '#450a0a', // darker variant
            ],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(
                ['name' => $status['name'], 'context' => $status['context']],
                [
                    'bg_color' => $status['bg_color'],
                    'text_color' => $status['text_color'],
                ]
            );
        }
    }
}
