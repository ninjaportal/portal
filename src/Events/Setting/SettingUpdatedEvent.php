<?php

namespace NinjaPortal\Portal\Events\Setting;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Setting;

class SettingUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Setting $setting) {}
}

