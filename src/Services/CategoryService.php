<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Services\CategoryServiceInterface;
use NinjaPortal\Portal\Utils;

class CategoryService extends BaseService implements CategoryServiceInterface
{
    public static function getModel(): string
    {
        return Utils::getCategoryModel();
    }
}
