<?php

namespace NinjaPortal\Portal\Events\Menu;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\MenuItem;

class MenuItemCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public MenuItem $menuItem) {}
}

