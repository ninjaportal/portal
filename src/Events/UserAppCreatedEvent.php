<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lordjoo\LaraApigee\Entities\EntityInterface;

class UserAppCreatedEvent
{
    use Dispatchable, SerializesModels;

    public string $userEmail;

    public EntityInterface $app;

    public function __construct(EntityInterface $app, string $userEmail)
    {
        $this->userEmail = $userEmail;
        $this->app = $app;
    }
}
