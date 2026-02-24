<?php

namespace NinjaPortal\Portal\Events\Menu;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Menu;

class MenuCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Menu $menu) {}
}

