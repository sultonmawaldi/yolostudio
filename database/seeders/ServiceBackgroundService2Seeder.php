<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceBackground;

class ServiceBackgroundService2Seeder extends Seeder
{
    public function run(): void
    {
        $serviceId = 3;

        $backgrounds = [
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
