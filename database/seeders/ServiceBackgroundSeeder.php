<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceBackground;

class ServiceBackgroundService2Seeder extends Seeder
{
    public function run(): void
    {
        $serviceId = 2;

        $backgrounds = [
            [
                'service_id' => $serviceId,
                'name' => 'Light Grey',
                'type' => 'color',
                'value' => '#f3f4f6',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Natural Grey',
                'type' => 'color',
                'value' => '#9ca3af',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Beige',
                'type' => 'color',
                'value' => '#f5f5dc',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'White',
                'type' => 'color',
                'value' => '#ffffff',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Red',
                'type' => 'color',
                'value' => '#dc2626',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Blue',
                'type' => 'color',
                'value' => '#2563eb',
                'sort_order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($backgrounds as $bg) {
            ServiceBackground::updateOrCreate(
                [
                    'service_id' => $bg['service_id'],
                    'value' => $bg['value'], // unik per service
                ],
                $bg
            );
        }
    }
}
