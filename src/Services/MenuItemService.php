<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Services\MenuItemServiceInterface;
use NinjaPortal\Portal\Utils;

class MenuItemService extends BaseService implements MenuItemServiceInterface
{
    public static function getModel(): string
    {
        return Utils::getMenuItemModel();
    }
}
