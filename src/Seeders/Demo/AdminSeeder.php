<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use NinjaPortal\Portal\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admins = [
            [
                'name' => 'Portal Owner',
                'email' => 'admin@ninjaportal.test',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Support Admin',
                'email' => 'support.admin@ninjaportal.test',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($admins as $admin) {
            Admin::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );
        }
    }
}
