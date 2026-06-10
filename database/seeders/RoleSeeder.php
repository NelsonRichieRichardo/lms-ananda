<?php

namespace Database\Seeders;

use App\Support\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([RoleName::SuperAdmin, RoleName::Teacher, RoleName::Student] as $name) {
            Role::query()->firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }
    }
}
