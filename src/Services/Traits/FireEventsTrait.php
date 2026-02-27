<?php

namespace NinjaPortal\Portal\Services\Traits;

trait FireEventsTrait
{
    abstract public function getModel();

    protected function fireEvent(
        string $action,
        array $eventParams = []
    ): void {
        $eventClass = $this->resolveEventClass($action);

        if (class_exists($eventClass)) {
            event(new $eventClass(...$eventParams));
        }
    }

    protected function resolveEventClass(string $action): string
    {
        $model = class_basename($this->getModel());
        $eventBase = $model.ucfirst($action).'Event';
        $namespace = 'NinjaPortal\\Portal\\Events';

        // Most CRUD/domain events are grouped by model namespace (Events\User\UserCreatedEvent).
        $namespacedEvent = $namespace.'\\'.$model.'\\'.$eventBase;
        if (class_exists($namespacedEvent)) {
            return $namespacedEvent;
        }

        // Some cross-domain events intentionally live at the root (e.g. UserAppCredential*Event).
        return $namespace.'\\'.$eventBase;
    }
}
