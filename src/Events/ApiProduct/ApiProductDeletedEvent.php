<?php

namespace NinjaPortal\Portal\Events\ApiProduct;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductDeletedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public ApiProduct $apiProduct) {}
}

