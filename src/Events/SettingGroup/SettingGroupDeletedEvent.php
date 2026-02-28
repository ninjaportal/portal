<?php

namespace NinjaPortal\Portal\Events\SettingGroup;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\SettingGroup;

class SettingGroupDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public SettingGroup $settingGroup) {}
}
