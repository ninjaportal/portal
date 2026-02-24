<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialProductRevokedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $email,
        public string $appID,
        public string $api_product,
        public ?string $credentialKey = null
    ) {}
}
