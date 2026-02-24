<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialDeletedEvent
{
    use Dispatchable, SerializesModels;

    public string $email;

    public string $appID;

    public ?string $credentialKey;

    public function __construct(string $email, string $appID, ?string $credentialKey = null)
    {
        $this->email = $email;
        $this->appID = $appID;
        $this->credentialKey = $credentialKey;
    }
}
