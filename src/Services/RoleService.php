<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\RoleRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\RoleServiceInterface;
use NinjaPortal\Portal\Events\Role\RolePermissionsSyncedEvent;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * @mixin Traits\HasRepositoryAwareTrait<Role, RoleRepositoryInterface>
 */
class RoleService extends BaseService implements RoleServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(RoleRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public function syncPermissions(Role|int|string $role, array $permissionIds): Role
    {
        $role = $this->repository()->resolve($role);

        $this->callHook('beforeSyncPermissions', [$role, $permissionIds]);

        $guard = $role->guard_name;
        $ids = Permission::query()
            ->where('guard_name', $guard)
            ->whereIn('id', $permissionIds)
            ->pluck('id')
            ->all();

        $role->permissions()->sync($ids);
        $role->load('permissions');

        $this->callHook('afterSyncPermissions', [$role, $ids]);

        RolePermissionsSyncedEvent::dispatch($role, $ids);

        return $role;
    }
}
