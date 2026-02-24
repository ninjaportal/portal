<?php

namespace NinjaPortal\Portal\Events\ApiProduct;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public ApiProduct $apiProduct) {}
}

