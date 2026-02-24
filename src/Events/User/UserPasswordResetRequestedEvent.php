<?php

namespace NinjaPortal\Portal\Events\User;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\User;

class UserPasswordResetRequestedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $user,
        public string $token
    ) {}
}
