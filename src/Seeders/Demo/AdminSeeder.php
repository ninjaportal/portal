<?php

namespace NinjaPortal\Portal\Seeders\Demo;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use NinjaPortal\Portal\Models\Admin;
use Spatie\Permission\Models\Role;

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
            $adminModel = Admin::updateOrCreate(
                ['email' => $admin['email']],
                $admin
            );

            $this->assignDefaultRole($adminModel);
        }
    }

    protected function assignDefaultRole(Admin $admin): void
    {
        if (! method_exists($admin, 'assignRole')) {
            return;
        }

        $adminGuard = (string) config('portal-api.auth.guards.admin', 'admin');
        $role = Role::query()
            ->where('name', 'super_admin')
            ->where('guard_name', $adminGuard)
            ->first();

        if (! $role) {
            return;
        }

        if (method_exists($admin, 'hasRole') && $admin->hasRole($role)) {
            return;
        }

        $admin->assignRole($role);
    }
}
