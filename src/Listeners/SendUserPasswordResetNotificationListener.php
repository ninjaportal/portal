<?php

namespace NinjaPortal\Portal\Listeners;

use NinjaPortal\Portal\Events\User\UserPasswordResetRequestedEvent;

class SendUserPasswordResetNotificationListener
{
    public function handle(UserPasswordResetRequestedEvent $event): void
    {
        if (! method_exists($event->user, 'sendPasswordResetNotification')) {
            return;
        }

        $event->user->sendPasswordResetNotification($event->token);
    }
}
