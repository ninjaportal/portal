<?php

namespace NinjaPortal\Portal\Seeders;

use Illuminate\Database\Seeder;
use NinjaPortal\Portal\Utils;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    /**
     * Core policy-managed models in the portal package.
     *
     * @var array<int, string>
     */
    protected array $policyModels = [
        'admin',
        'user',
        'api_product',
        'audience',
        'category',
        'menu',
        'menu_item',
        'setting',
        'setting_group',
        'role',
    ];

    /**
     * Policy abilities supported by BasePolicy.
     *
     * @var array<int, string>
     */
    protected array $policyAbilities = [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force_delete',
    ];

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = array_values(array_unique(array_merge(
            $this->accessPermissions(),
            $this->policyPermissions(),
        )));

        $rolesByGuard = [];

        foreach ($this->targetGuards() as $guard) {
            foreach ($permissions as $permission) {
                Permission::query()->firstOrCreate(
                    ['name' => $permission, 'guard_name' => $guard],
                    ['name' => $permission, 'guard_name' => $guard],
                );
            }

            $role = Role::query()->firstOrCreate(
                ['name' => 'super_admin', 'guard_name' => $guard],
                ['name' => 'super_admin', 'guard_name' => $guard],
            );

            $role->syncPermissions($permissions);
            $rolesByGuard[$guard] = $role;
        }

        $this->assignAdminRoles($rolesByGuard);
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Route/API level permissions used by the admin API package and app UI.
     *
     * @return array<int, string>
     */
    protected function accessPermissions(): array
    {
        return [
            'portal.admin.access',
            'portal.rbac.manage',
            'portal.admins.manage',
            'portal.activities.view',
        ];
    }

    /**
     * Model policy permissions using the standardized convention:
     * `portal.{model}.{ability}`
     *
     * @return array<int, string>
     */
    protected function policyPermissions(): array
    {
        $permissions = [];

        foreach ($this->policyModels as $model) {
            foreach ($this->policyAbilities as $ability) {
                $permissions[] = "portal.{$model}.{$ability}";
            }
        }

        return $permissions;
    }

    /**
     * @return array<int, string>
     */
    protected function targetGuards(): array
    {
        $guards = [
            'web',
            Utils::getAdminRbacGuard(),
            (string) config('auth.defaults.guard', 'web'),
            (string) config('portal-api.auth.guards.admin', Utils::getAdminRbacGuard()),
            (string) config('portal-admin.panel.rbac_guard', Utils::getAdminRbacGuard()),
        ];

        $guards = array_map(
            static fn ($guard) => trim((string) $guard),
            $guards,
        );

        return array_values(array_unique(array_filter($guards)));
    }

    /**
     * Assign super admin role for all seeded admins on the admin guard.
     *
     * @param  array<string, Role>  $rolesByGuard
     */
    protected function assignAdminRoles(array $rolesByGuard): void
    {
        $adminModel = Utils::getAdminModel();
        if (! is_string($adminModel) || trim($adminModel) === '' || ! class_exists($adminModel)) {
            return;
        }

        $adminGuard = (string) config('portal-admin.panel.rbac_guard', config('portal-api.auth.guards.admin', Utils::getAdminRbacGuard()));
        $role = $rolesByGuard[$adminGuard] ?? Role::query()
            ->where('name', 'super_admin')
            ->where('guard_name', $adminGuard)
            ->first();

        if (! $role) {
            return;
        }

        $admins = $adminModel::query()->orderBy('id')->get();
        foreach ($admins as $admin) {
            if (! method_exists($admin, 'assignRole')) {
                continue;
            }

            if (method_exists($admin, 'hasRole') && $admin->hasRole($role)) {
                continue;
            }

            $admin->assignRole($role);
        }
    }
}
