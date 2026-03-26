<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Outlet;
use Illuminate\Support\Str;

class OutletSeeder extends Seeder
{
    public function run(): void
    {
        $outlets = [
            [
                'name' => 'Outlet Cilegon',
                'slug' => Str::slug('Outlet Cilegon'),
                'address' => 'Cilegon, Banten',
                'phone' => '081234567801',
                'google_maps' => 'https://maps.google.com/?q=Cilegon+Banten',
                'image' => 'outlets/cilegon.jpg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outlet Serang',
                'slug' => Str::slug('Outlet Serang'),
                'address' => 'Serang, Banten',
                'phone' => '081234567802',
                'google_maps' => 'https://maps.google.com/?q=Serang+Banten',
                'image' => 'outlets/serang.jpg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Outlet Pandeglang',
                'slug' => Str::slug('Outlet Pandeglang'),
                'address' => 'Pandeglang, Banten',
                'phone' => '081234567803',
                'google_maps' => 'https://maps.google.com/?q=Pandeglang+Banten',
                'image' => 'outlets/pandeglang.jpg',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Outlet::insert($outlets);
    }
}
