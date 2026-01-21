<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Addon;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            ['code' => 'extra_person', 'name' => 'Tambahan Orang', 'price' => 15000, 'unit' => 'person'],
            ['code' => 'photobox_bundle', 'name' => 'Bundling Photobox', 'price' => 10000, 'unit' => 'person'],
            ['code' => 'extra_time', 'name' => 'Tambahan Waktu', 'price' => 5000, 'unit' => 'minute', 'max_qty' => 5],
            ['code' => 'print_photo', 'name' => 'Cetak Foto', 'price' => 5000],
            ['code' => 'print_special', 'name' => 'Cetak Spesial Foto', 'price' => 15000],
            ['code' => 'keychain_stainless', 'name' => 'Keychain Stainless', 'price' => 25000],
            ['code' => 'keychain_hook', 'name' => 'Gantungan', 'price' => 15000],
            ['code' => 'keychain_bundle', 'name' => 'Keychain + Gantungan', 'price' => 35000],
            ['code' => 'keychain_plastic', 'name' => 'Keychain Plastik', 'price' => 10000],
        ];

        foreach ($addons as $addon) {
            Addon::updateOrCreate(
                ['code' => $addon['code']],
                $addon
            );
        }
    }
}
