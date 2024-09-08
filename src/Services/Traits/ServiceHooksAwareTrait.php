<?php

namespace NinjaPortal\Portal\Services\Traits;

trait ServiceHooksAwareTrait
{

    protected function callHook(
        string $hook,
        array  $params = []
    ): void
    {

        if (!method_exists($this, $hook)) {
            return;
        }

        $this->$hook(...$params);
    }


}
