<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class EmployeePermissionSeeder extends Seeder
{
    public function run()
    {
        // Ambil role employee
        $role = Role::firstOrCreate(['name' => 'employee', 'guard_name' => 'web']);

        // Daftar permission yang ingin diberikan ke employee
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'services.view',
            'services.create',
            'services.edit',
            'services.delete',
            'coupons.view',
            'coupons.create',
            'coupons.edit',
            'coupons.delete',
            'gallery.view',
            'gallery.create',
            'gallery.edit',
            'gallery.delete',
            'studio.view',
            'studio.create',
            'studio.edit',
            'studio.delete',
            'addons.view',
            'addons.create',
            'addons.edit',
            'addons.delete',
            'service-backgrounds.view',
            'service-backgrounds.create',
            'service-backgrounds.edit',
            'service-backgrounds.delete',
            'settings.edit'
        ];

        // Pastikan semua permission ada
        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Assign semua permission ke role employee
        $role->syncPermissions($permissions);

        echo "Employee permissions setup complete.\n";
    }
}
