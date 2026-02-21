<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        // ... optionally add specific permissions later if needed
        // For now, we will just use Role-based access control (RBAC) 
        // to keep it simple and effective.

        // create roles
        $roleSuperAdmin = Role::create(['name' => 'Super Admin']);
        $roleAdmin = Role::create(['name' => 'Admin']);
        $roleKasir = Role::create(['name' => 'Kasir']);
        $roleGudang = Role::create(['name' => 'Gudang']);

        // Create Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@inventory.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $superAdmin->assignRole($roleSuperAdmin);

        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@inventory.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($roleAdmin);

        // Create Kasir User
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@inventory.com'],
            [
                'name' => 'Kasir Toko',
                'password' => Hash::make('password'),
            ]
        );
        $kasir->assignRole($roleKasir);

        // Create Gudang User
        $gudang = User::updateOrCreate(
            ['email' => 'gudang@inventory.com'],
            [
                'name' => 'Staf Gudang',
                'password' => Hash::make('password'),
            ]
        );
        $gudang->assignRole($roleGudang);
    }
}
