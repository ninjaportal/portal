<?php

namespace NinjaPortal\Portal\Events\UserApp;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lordjoo\LaraApigee\Entities\EntityInterface;

class UserAppApprovedEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $userEmail, public EntityInterface $app) {}
}

