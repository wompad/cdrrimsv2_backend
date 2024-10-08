<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create Permissions
        $viewPermission = Permission::create(['name' => 'view']);
        $editPermission = Permission::create(['name' => 'edit']);

        // Create Roles and assign created permissions
        $admin = Role::create(['name' => 'Admin']);
        $user = Role::create(['name' => 'User']);

        // Give permissions to roles
        $admin->givePermissionTo([$viewPermission, $editPermission]);
        $user->givePermissionTo($viewPermission);
    }
}
