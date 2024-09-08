<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAppCredentialProductRemoved
{
    use Dispatchable, SerializesModels;

    public string $email;
    public string $appID;
    public string $api_product;

    public function __construct(string $email, string $appID, string $api_product)
    {
        $this->email = $email;
        $this->appID = $appID;
        $this->api_product = $api_product;
    }

}
