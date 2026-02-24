<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\MenuRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\MenuServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;
use NinjaPortal\Portal\Utils;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\Menu, MenuRepositoryInterface>
 */
class MenuService extends BaseService implements MenuServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(MenuRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getModel(): string
    {
        return Utils::getMenuModel();
    }
}
