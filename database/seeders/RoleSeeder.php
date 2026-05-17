<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            // Level 1 — Executive
            ['name' => 'CEO',             'level' => 100, 'is_system' => true],
            ['name' => 'COO',             'level' => 90,  'is_system' => false],
            ['name' => 'CTO',             'level' => 85,  'is_system' => false],
            ['name' => 'CFO',             'level' => 80,  'is_system' => false],

            // Level 2 — Portofolio
            ['name' => 'VP',              'level' => 60,  'is_system' => false],

            // Level 3 — Project
            ['name' => 'PM',              'level' => 40,  'is_system' => false],

            // Level 4 — Eksekusi
            ['name' => 'Team Lead',       'level' => 20,  'is_system' => false],
            ['name' => 'Developer',       'level' => 10,  'is_system' => false],

            // System — invisible
            ['name' => 'Programmer',      'level' => 999, 'is_system' => true],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
