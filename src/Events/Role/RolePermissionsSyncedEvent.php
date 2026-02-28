<?php

namespace NinjaPortal\Portal\Events\Role;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class RolePermissionsSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $permissionIds
     */
    public function __construct(public Role $role, public array $permissionIds) {}
}
