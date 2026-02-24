<?php

namespace NinjaPortal\Portal\Events\User;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\User;

class UserAudiencesSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $audienceIds
     */
    public function __construct(public User $user, public array $audienceIds) {}
}

