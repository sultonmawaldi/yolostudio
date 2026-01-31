<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pricelist;
use App\Models\Service;

class PricelistSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil semua service
        $services = Service::all();

        foreach ($services as $service) {
            Pricelist::create([
                'service_id'   => $service->id,
                'title'        => $service->name . ' Basic Package',
                'description'  => 'Paket standar untuk ' . $service->name,
                'price'        => rand(100000, 500000),
                'features'     => ['Feature 1', 'Feature 2', 'Feature 3'],
                'category'     => 'Standard',
                'button_text'  => 'Booking',
                'button_link'  => '/booking?service_id=' . $service->id,
                'is_active'    => true,
                'sort_order'   => 1,
            ]);

            Pricelist::create([
                'service_id'   => $service->id,
                'title'        => $service->name . ' Premium Package',
                'description'  => 'Paket premium untuk ' . $service->name,
                'price'        => rand(500000, 1000000),
                'features'     => ['Feature A', 'Feature B', 'Feature C'],
                'category'     => 'Premium',
                'button_text'  => 'Booking',
                'button_link'  => '/booking?service_id=' . $service->id,
                'is_active'    => true,
                'sort_order'   => 2,
            ]);
        }
    }
}
