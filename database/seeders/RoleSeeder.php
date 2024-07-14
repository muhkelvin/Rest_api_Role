<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat peran
        $adminRole = Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
        Role::create(['name' => 'user payment']);

        // Membuat izin
        Permission::create(['name' => 'manage books']);
        Permission::create(['name' => 'manage payments']);

        // Memberikan izin ke peran admin
        $adminRole->givePermissionTo(['manage books', 'manage payments']);
    }
}
