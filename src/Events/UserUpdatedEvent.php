<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use NinjaPortal\Portal\Models\User;

class UserUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        Log::info('UserUpdatedEvent');
        $this->user = $user;
    }

}
