<?php

namespace NinjaPortal\Portal\Services;

use NinjaPortal\Portal\Contracts\Repositories\MenuItemRepositoryInterface;
use NinjaPortal\Portal\Contracts\Services\MenuItemServiceInterface;
use NinjaPortal\Portal\Services\Traits\CrudOperationsTrait;

/**
 * @mixin Traits\HasRepositoryAwareTrait<\NinjaPortal\Portal\Models\MenuItem, MenuItemRepositoryInterface>
 */
class MenuItemService extends BaseService implements MenuItemServiceInterface
{
    use CrudOperationsTrait;

    public function __construct(MenuItemRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }
}
