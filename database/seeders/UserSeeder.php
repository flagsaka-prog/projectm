<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'CEO',       'email' => 'ceo@projectm.test',       'role' => 'CEO',       'active' => true],
            ['name' => 'COO',       'email' => 'coo@projectm.test',       'role' => 'COO',       'active' => true],
            ['name' => 'CTO',       'email' => 'cto@projectm.test',       'role' => 'CTO',       'active' => true],
            ['name' => 'CFO',       'email' => 'cfo@projectm.test',       'role' => 'CFO',       'active' => true],
            ['name' => 'VP',        'email' => 'vp@projectm.test',        'role' => 'VP',        'active' => true],
            ['name' => 'PM',        'email' => 'pm@projectm.test',        'role' => 'PM',        'active' => true],
            ['name' => 'Team Lead', 'email' => 'teamlead@projectm.test',  'role' => 'Team Lead', 'active' => true],
            ['name' => 'Developer', 'email' => 'developer@projectm.test', 'role' => 'Developer', 'active' => true],
            ['name' => 'Programmer', 'email' => 'programmer@projectm.test', 'role' => 'Programmer', 'active' => true],
        ];

        foreach ($users as $u) {
            $user = User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name'      => $u['name'],
                    'password'  => bcrypt('password'),
                    'is_active' => $u['active'],
                ]
            );
            $user->syncRoles([$u['role']]);
        }
    }
}
