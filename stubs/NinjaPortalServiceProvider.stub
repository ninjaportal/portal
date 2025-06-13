<?php

namespace App\Providers;


use NinjaPortal\Portal\Providers\BasePortalServiceProvider as Provider;
use NinjaPortal\Portal\Events;
use NinjaPortal\Portal\Listeners;

class NinjaPortalServiceProvider extends Provider
{

    /**
     * @var array
     * The event handler mappings for the application.
     */
    protected $listen = [
        Events\UserCreatedEvent::class => [
            Listeners\SyncUserWithApigeeListener::class,
        ],
        Events\UserUpdatedEvent::class => [
            Listeners\SyncUserWithApigeeListener::class,
        ],
    ];

    /**
     * @var array
     * The subscriber classes to register.
     */
    protected $observers = [

    ];

}
