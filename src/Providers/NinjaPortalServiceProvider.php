<?php

namespace NinjaPortal\Portal\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use NinjaPortal\Portal\Models;
use NinjaPortal\Portal\Observers;
use NinjaPortal\Portal\Events;
use NinjaPortal\Portal\Listeners;

class NinjaPortalServiceProvider extends EventServiceProvider
{

    /**
     * @var array
     * The event handler mappings for the application.
     */
    protected $listen = [
        Events\UserCreatedEvent::class => [
            Listeners\Edge\SyncUserWithApigeeListener::class,
        ],
        Events\UserUpdatedEvent::class => [
            Listeners\Edge\SyncUserWithApigeeListener::class,
        ],
    ];

    /**
     * @var array
     * The subscriber classes to register.
     */
    protected $observers = [

    ];

}
