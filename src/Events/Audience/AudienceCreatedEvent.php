<?php

namespace NinjaPortal\Portal\Events\Audience;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use NinjaPortal\Portal\Models\Audience;

class AudienceCreatedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Audience $audience) {}
}

