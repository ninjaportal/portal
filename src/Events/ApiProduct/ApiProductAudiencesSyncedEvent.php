<?php

namespace NinjaPortal\Portal\Events\ApiProduct;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\ApiProduct;

class ApiProductAudiencesSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $audienceIds
     */
    public function __construct(public ApiProduct $apiProduct, public array $audienceIds) {}
}

