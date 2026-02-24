<?php

namespace NinjaPortal\Portal\Services\Traits;

trait FireEventsTrait
{
    abstract public function getModel();

    protected function fireEvent(
        string $action,
        array $eventParams = []
    ): void {
        // Canonical event names use the `...Event` suffix only.
        $modelBase = class_basename($this->getModel()).ucfirst($action);
        $namespace = 'NinjaPortal\\Portal\\Events';

        $eventClass = $namespace.'\\'.$modelBase.'Event';

        if (class_exists($eventClass)) {
            event(new $eventClass(...$eventParams));
        }
    }
}
