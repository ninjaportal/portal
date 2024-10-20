<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Services\MenuServiceInterface;
use NinjaPortal\Portal\Utils;

class MenuService extends BaseService implements MenuServiceInterface
{
    public static function getModel(): string
    {
        return Utils::getMenuModel();
    }
}
