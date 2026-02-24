<?php

namespace NinjaPortal\Portal\Events\Admin;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Admin;

class AdminCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Admin $admin) {}
}

