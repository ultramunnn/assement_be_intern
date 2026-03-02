<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'api'],
            ['name' => 'admin', 'guard_name' => 'api']
        );

        Role::firstOrCreate(
            ['name' => 'user', 'guard_name' => 'api'],
            ['name' => 'user', 'guard_name' => 'api']
        );
    }
}
