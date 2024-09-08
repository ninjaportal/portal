<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialProductAdded
{
    use Dispatchable, SerializesModels;

    public string $email;
    public string $appID;
    public array $api_products;

    public function __construct(string $email, string $appID, array $api_products)
    {
        $this->email = $email;
        $this->appID = $appID;
        $this->api_products = $api_products;
    }

}
