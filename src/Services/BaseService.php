<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Services\ServiceInterface;

abstract class BaseService implements ServiceInterface
{
    public function __construct() {}

    protected function mutateDataBeforeUpdate(array $data): array
    {
        return $data;
    }

    protected function mutateDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
