<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialProductAddedEvent
{
    use Dispatchable, SerializesModels;

    public string $email;

    public string $appID;

    public array $api_products;

    public ?string $credentialKey;

    public function __construct(string $email, string $appID, array $api_products, ?string $credentialKey = null)
    {
        $this->email = $email;
        $this->appID = $appID;
        $this->api_products = $api_products;
        $this->credentialKey = $credentialKey;
    }
}
