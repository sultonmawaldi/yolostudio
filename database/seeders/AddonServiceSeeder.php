<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddonServiceSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $serviceId = 7; // service yang mau di-seed

        // Hapus data lama supaya bersih
        DB::table('addon_service')->where('service_id', $serviceId)->delete();

        // Ambil semua addon yang aktif
        $addonIds = DB::table('addons')
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();

        $data = [];

        // Masukkan addon_id = 11 dulu supaya muncul paling awal
        if (($key = array_search(11, $addonIds)) !== false) {
            $data[] = [
                'service_id' => $serviceId,
                'addon_id'   => 11,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            unset($addonIds[$key]); // hapus dari array agar tidak duplikat
        }

        // Masukkan sisanya
        foreach ($addonIds as $addonId) {
            $data[] = [
                'service_id' => $serviceId,
                'addon_id'   => $addonId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Insert ke database
        DB::table('addon_service')->insert($data);
    }
}
