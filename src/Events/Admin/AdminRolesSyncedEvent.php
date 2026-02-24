<?php

namespace NinjaPortal\Portal\Events\Admin;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Admin;

class AdminRolesSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $roleIds
     */
    public function __construct(public Admin $admin, public array $roleIds) {}
}

