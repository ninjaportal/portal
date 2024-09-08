<?php

namespace NinjaPortal\Portal\Services\Traits;

trait FireEventsTrait
{

    abstract protected function getModel(): string;

    protected function fireEvent(
        string $action,
        array  $eventParams = []
    ): void
    {
        $modelName = class_basename($this->getModel()) . ucfirst($action) . "Event";
        $namespace = "NinjaPortal\Portal\Events";
        $event = $namespace . "\\" . $modelName;
        if (class_exists($event)) {
            event(new $event(...$eventParams));
        }
    }

}
