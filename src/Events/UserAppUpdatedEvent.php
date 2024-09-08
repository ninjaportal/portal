<?php

namespace NinjaPortal\Portal\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Lordjoo\LaraApigee\Entities\EntityInterface;

class UserAppUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public string $userEmail;

    public EntityInterface $app;

    public function __construct(string $userEmail, EntityInterface $app)
    {
        $this->userEmail = $userEmail;
        $this->app = $app;
    }
}
