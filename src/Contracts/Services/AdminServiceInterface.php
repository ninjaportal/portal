<?php

namespace NinjaPortal\Portal\Contracts\Services;

use NinjaPortal\Portal\Models\Admin;

interface AdminServiceInterface extends ServiceInterface
{
    public function findByEmail(string $email): ?Admin;

    /**
     * @param  array<int, int|string>  $roleIds
     */
    public function syncRoles(Admin|int|string $admin, array $roleIds): Admin;
}
