<?php

namespace NinjaPortal\Portal\Events\Permission;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Spatie\Permission\Models\Permission;

class PermissionCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Permission $permission) {}
}
