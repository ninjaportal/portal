<?php

namespace NinjaPortal\Portal\Events\Audience;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Audience;

class AudienceProductsSyncedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @param  array<int, int|string>  $apiProductIds
     */
    public function __construct(public Audience $audience, public array $apiProductIds) {}
}

