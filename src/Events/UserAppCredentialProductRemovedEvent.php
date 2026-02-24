<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialProductRemovedEvent
{
    use Dispatchable, SerializesModels;

    public string $email;

    public string $appID;

    public string $api_product;

    public ?string $credentialKey;

    public function __construct(string $email, string $appID, string $api_product, ?string $credentialKey = null)
    {
        $this->email = $email;
        $this->appID = $appID;
        $this->api_product = $api_product;
        $this->credentialKey = $credentialKey;
    }
}
