<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonServiceSeeder extends Seeder
{
    public function run()
    {
        $service = Service::where('title', 'Personal Self Photo Studio')->first();

        if (!$service) {
            $this->command->warn('Service "Personal Self Photo Studio" not found');
            return;
        }

        $addonIds = Addon::where('is_active', 1)->pluck('id');

        $service->addons()->syncWithoutDetaching($addonIds);

        $this->command->info('Addon successfully attached to service Photobox');
    }
}
