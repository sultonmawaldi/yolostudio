<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceBackground;

class ServiceBackgroundSeeder extends Seeder
{
    public function run(): void
    {
        $serviceId = 3;

        $backgrounds = [
            [
                'service_id' => $serviceId,
                'name' => 'White',
                'type' => 'color',
                'value' => '#ffffff',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Natural grey',
                'type' => 'color',
                'value' => '#9ca3af',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Light Grey',
                'type' => 'color',
                'value' => '#e5e7eb',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Beige',
                'type' => 'color',
                'value' => '#e6d3b1',
                'sort_order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($backgrounds as $bg) {
            ServiceBackground::updateOrCreate(
                [
                    'service_id' => $bg['service_id'],
                    'value' => $bg['value'],
                ],
                $bg
            );
        }
    }
}
