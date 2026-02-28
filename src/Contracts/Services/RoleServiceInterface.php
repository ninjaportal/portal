<?php

namespace NinjaPortal\Portal\Contracts\Services;

use Spatie\Permission\Models\Role;

/**
 * Role management contract.
 */
interface RoleServiceInterface extends ServiceInterface
{
    /**
     * @param  array<int, int|string>  $permissionIds
     */
    public function syncPermissions(Role|int|string $role, array $permissionIds): Role;
}
