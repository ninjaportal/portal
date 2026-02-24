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

        $guard = (string) config('auth.defaults.guard', 'web');

        $permissions = array_values(array_unique(array_merge(
            $this->accessPermissions(),
            $this->policyPermissions(),
        )));

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

        $adminModel = Utils::getAdminModel();
        if (! $adminModel || ! class_exists($adminModel)) {
            return;
        }

        $firstAdmin = $adminModel::query()->orderBy('id')->first();
        if (! $firstAdmin) {
            return;
        }

        if (method_exists($firstAdmin, 'hasAnyRole') && $firstAdmin->hasAnyRole(['super_admin'])) {
            return;
        }

        if (method_exists($firstAdmin, 'assignRole')) {
            $firstAdmin->assignRole('super_admin');
        }
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
}
