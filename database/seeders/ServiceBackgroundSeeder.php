<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceBackground;

class ServiceBackgroundSeeder extends Seeder
{
    public function run(): void
    {
        $serviceId = 1;

        $backgrounds = [
            [
                'service_id' => $serviceId,
                'name' => 'Putih',
                'value' => '#ffffff',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Hitam',
                'value' => '#000000',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'service_id' => $serviceId,
                'name' => 'Biru',
                'value' => '#2563eb',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($backgrounds as $bg) {
            ServiceBackground::updateOrCreate(
                [
                    'service_id' => $bg['service_id'],
                    'value' => $bg['value'], // 🔑 unique logic
                ],
                $bg
            );
        }
    }
}
