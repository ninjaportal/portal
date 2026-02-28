<?php

namespace NinjaPortal\Portal\Events\Role;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Role;

class RoleDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Role $role) {}
}
