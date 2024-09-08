<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialRevoked
{
    use Dispatchable, SerializesModels;

    public string $email;
    public string $appID;

    public function __construct(string $email, string $appID)
    {
        $this->email = $email;
        $this->appID = $appID;
    }
}
