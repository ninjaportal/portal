<?php

namespace NinjaPortal\Portal\Events\Audience;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Audience;

class AudienceUsersSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $userIds
     */
    public function __construct(public Audience $audience, public array $userIds) {}
}

