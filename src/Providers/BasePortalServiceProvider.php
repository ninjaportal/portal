<?php

namespace NinjaPortal\Portal\Providers;

use Closure;
use Illuminate\Support\Facades\Event;
use NinjaPortal\Portal\Events;
use NinjaPortal\Portal\Listeners;
use Illuminate\Support\ServiceProvider;


abstract class BasePortalServiceProvider extends ServiceProvider
{

    /**
     * @var array
     * The event handler mappings for the application.
     */
    protected $listen = [];

    /**
     * @var array
     * The subscriber classes to register.
     */
    protected $observers = [];


    public function register()
    {
        $this->booting(function () {
            $events = $this->listens();

            foreach ($events as $event => $listeners) {
                foreach (array_unique($listeners, SORT_REGULAR) as $listener) {
                    Event::listen($event, $listener);
                }
            }


            foreach ($this->observers as $model => $observers) {
                $model::observe($observers);
            }
        });
    }


    public function boot()
    {

    }

    /**
     * Get the events and listeners.
     *
     * @return array
     */
    protected function listens()
    {
        return $this->listen;
    }

}
